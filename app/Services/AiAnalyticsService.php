<?php
// app/Services/AiAnalyticsService.php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AiAnalyticsService
{
    protected $openAiApiKey;
    protected $useMockData = false;

    public function __construct()
    {
        $this->openAiApiKey = config('services.openai.api_key');

        // Use mock data if OpenAI API key is not set
        if (empty($this->openAiApiKey) || $this->openAiApiKey === 'your-openai-api-key-here') {
            $this->useMockData = true;
            Log::info('AiAnalyticsService: Using mock data (OpenAI API key not configured)');
        }
    }

    /**
     * Get AI-powered insights for debt management
     */
    public function getDebtInsights($period = '30d')
    {
        try {
            // Collect data
            $data = $this->collectAnalyticsData($period);

            // Generate insights
            if ($this->useMockData) {
                return $this->getMockInsights($data);
            }

            return $this->generateAiInsights($data);

        } catch (\Exception $e) {
            Log::error('Failed to get debt insights: ' . $e->getMessage());
            return $this->getFallbackInsights();
        }
    }

    /**
     * Collect analytics data
     */
    private function collectAnalyticsData($period)
    {
        $startDate = $this->getStartDate($period);

        return [
            'summary' => $this->getDebtSummary($startDate),
            'trends' => $this->getPaymentTrends($startDate),
            'top_debtors' => $this->getTopDebtors(5),
            'aging_analysis' => $this->getAgingAnalysis(),
            'period' => $period,
            'collected_at' => now()->toDateTimeString()
        ];
    }

    /**
     * Get debt summary
     */
    private function getDebtSummary($startDate)
    {
        try {
            $result = DB::table('consolidated_billings')
                ->select([
                    DB::raw('COALESCE(SUM(total_amount - paid_amount), 0) as total_outstanding'),
                    DB::raw('COALESCE(SUM(CASE WHEN due_date < NOW() AND total_amount > paid_amount THEN total_amount - paid_amount ELSE 0 END), 0) as overdue_amount'),
                    DB::raw('COALESCE(COUNT(CASE WHEN due_date < NOW() AND total_amount > paid_amount THEN 1 END), 0) as overdue_count'),
                    DB::raw('COALESCE(AVG(CASE WHEN due_date < NOW() AND total_amount > paid_amount THEN DATEDIFF(NOW(), due_date) END), 0) as avg_days_overdue'),
                    DB::raw('COALESCE((SUM(paid_amount) / NULLIF(SUM(total_amount), 0)) * 100, 0) as collection_rate'),
                    DB::raw('COALESCE(COUNT(DISTINCT user_id), 0) as active_customers')
                ])
                ->where('billing_date', '>=', $startDate)
                ->first();

            return [
                'total_outstanding' => floatval($result->total_outstanding ?? 0),
                'overdue_amount' => floatval($result->overdue_amount ?? 0),
                'overdue_count' => intval($result->overdue_count ?? 0),
                'avg_days_overdue' => floatval($result->avg_days_overdue ?? 0),
                'collection_rate' => floatval($result->collection_rate ?? 0),
                'active_customers' => intval($result->active_customers ?? 0)
            ];

        } catch (\Exception $e) {
            Log::error('Error getting debt summary: ' . $e->getMessage());
            return $this->getDefaultSummary();
        }
    }

    /**
     * Get payment trends
     */
    private function getPaymentTrends($startDate)
    {
        try {
            return DB::table('consolidated_billings')
                ->select([
                    DB::raw('DATE_FORMAT(payment_date, "%Y-%m-%d") as date'),
                    DB::raw('COALESCE(SUM(paid_amount), 0) as daily_collections'),
                    DB::raw('COALESCE(COUNT(*), 0) as payment_count')
                ])
                ->where('status', 'paid')
                ->where('payment_date', '>=', $startDate)
                ->whereNotNull('payment_date')
                ->groupBy(DB::raw('DATE_FORMAT(payment_date, "%Y-%m-%d")'))
                ->orderBy('date')
                ->limit(30)
                ->get()
                ->map(function($item) {
                    return [
                        'date' => $item->date,
                        'daily_collections' => floatval($item->daily_collections),
                        'payment_count' => intval($item->payment_count)
                    ];
                })
                ->toArray();

        } catch (\Exception $e) {
            Log::error('Error getting payment trends: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get top debtors
     */
    private function getTopDebtors($limit = 5)
    {
        try {
            return DB::table('consolidated_billings as cb')
                ->join('users as u', 'cb.user_id', '=', 'u.id')
                ->select([
                    'u.id',
                    'u.name',
                    DB::raw('COALESCE(SUM(cb.total_amount - cb.paid_amount), 0) as outstanding')
                ])
                ->whereRaw('cb.total_amount > cb.paid_amount')
                ->groupBy('u.id', 'u.name')
                ->orderByDesc('outstanding')
                ->limit($limit)
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'outstanding' => floatval($item->outstanding)
                    ];
                })
                ->toArray();

        } catch (\Exception $e) {
            Log::error('Error getting top debtors: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get aging analysis
     */
    private function getAgingAnalysis()
    {
        try {
            $result = DB::table('consolidated_billings')
                ->select([
                    DB::raw('COALESCE(SUM(CASE WHEN DATEDIFF(NOW(), due_date) BETWEEN 0 AND 30 AND total_amount > paid_amount THEN total_amount - paid_amount ELSE 0 END), 0) as current'),
                    DB::raw('COALESCE(SUM(CASE WHEN DATEDIFF(NOW(), due_date) BETWEEN 31 AND 60 AND total_amount > paid_amount THEN total_amount - paid_amount ELSE 0 END), 0) as days_31_60'),
                    DB::raw('COALESCE(SUM(CASE WHEN DATEDIFF(NOW(), due_date) BETWEEN 61 AND 90 AND total_amount > paid_amount THEN total_amount - paid_amount ELSE 0 END), 0) as days_61_90'),
                    DB::raw('COALESCE(SUM(CASE WHEN DATEDIFF(NOW(), due_date) > 90 AND total_amount > paid_amount THEN total_amount - paid_amount ELSE 0 END), 0) as days_over_90')
                ])
                ->whereRaw('total_amount > paid_amount')
                ->first();

            return [
                'current' => floatval($result->current ?? 0),
                'days_31_60' => floatval($result->days_31_60 ?? 0),
                'days_61_90' => floatval($result->days_61_90 ?? 0),
                'days_over_90' => floatval($result->days_over_90 ?? 0)
            ];

        } catch (\Exception $e) {
            Log::error('Error getting aging analysis: ' . $e->getMessage());
            return $this->getDefaultAgingAnalysis();
        }
    }

    /**
     * Generate AI insights using OpenAI
     */
    private function generateAiInsights($data)
    {
        try {
            $prompt = $this->buildAnalyticsPrompt($data);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->openAiApiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a financial analyst specializing in debt management and collections.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'temperature' => 0.7,
                'max_tokens' => 1000
            ]);

            if ($response->successful()) {
                $content = $response->json()['choices'][0]['message']['content'];

                // Try to parse as JSON, otherwise return as text
                $decoded = json_decode($content, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $decoded;
                }

                return ['text_insights' => $content];
            }

            throw new \Exception('OpenAI API request failed: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('OpenAI API error: ' . $e->getMessage());
            return $this->getMockInsights($data);
        }
    }

    /**
     * Build analytics prompt for AI
     */
    private function buildAnalyticsPrompt($data)
    {
        $summary = $data['summary'];
        $topDebtors = array_slice($data['top_debtors'], 0, 3);

        $prompt = "Analyze this debt management data and provide insights in JSON format with these sections:
        1. key_findings: List 3-5 most important findings
        2. risk_analysis: Identify high-risk areas and patterns
        3. recommendations: 3-5 specific actions to improve collections
        4. predictions: What to expect in the next 30 days
        5. alerts: Any urgent issues needing immediate attention

        Data Summary:
        - Total Outstanding: $" . number_format($summary['total_outstanding'], 2) . "
        - Overdue Amount: $" . number_format($summary['overdue_amount'], 2) . "
        - Overdue Invoices: " . $summary['overdue_count'] . "
        - Average Days Overdue: " . number_format($summary['avg_days_overdue'], 1) . "
        - Collection Rate: " . number_format($summary['collection_rate'], 2) . "%
        - Active Customers: " . $summary['active_customers'] . "

        Top 3 Debtors: ";

        foreach ($topDebtors as $debtor) {
            $prompt .= "\n- " . $debtor['name'] . ": $" . number_format($debtor['outstanding'], 2);
        }

        $prompt .= "\n\nAging Analysis:
        - Current (0-30 days): $" . number_format($data['aging_analysis']['current'], 2) . "
        - 31-60 days: $" . number_format($data['aging_analysis']['days_31_60'], 2) . "
        - 61-90 days: $" . number_format($data['aging_analysis']['days_61_90'], 2) . "
        - Over 90 days: $" . number_format($data['aging_analysis']['days_over_90'], 2) . "

        Please respond in valid JSON format only.";

        return $prompt;
    }

    /**
     * Get mock insights (for when OpenAI is not configured)
     */
    private function getMockInsights($data)
    {
        $summary = $data['summary'];

        return [
            'key_findings' => [
                'Total outstanding debt is $' . number_format($summary['total_outstanding'], 2),
                'Overdue amount represents ' . ($summary['total_outstanding'] > 0 ?
                    number_format(($summary['overdue_amount'] / $summary['total_outstanding']) * 100, 1) : 0) . '% of total debt',
                'Average collection delay is ' . number_format($summary['avg_days_overdue'], 1) . ' days',
                'Collection success rate is ' . number_format($summary['collection_rate'], 1) . '%'
            ],
            'risk_analysis' => [
                'High concentration in top debtors',
                'Aging debt over 90 days needs immediate attention',
                'Collection rate below optimal target of 85%'
            ],
            'recommendations' => [
                'Implement automated payment reminders for overdue accounts',
                'Offer payment plans for debts over $5,000',
                'Prioritize collection efforts on debts over 60 days',
                'Review and update credit policies for repeat offenders'
            ],
            'predictions' => [
                'Expected collections in next 30 days: Based on current trends',
                'High-risk accounts may require escalation',
                'Consider write-offs for debts over 120 days'
            ],
            'alerts' => [
                'Monitor top 3 debtors closely',
                'Review aging debt weekly',
                'Update collection strategies monthly'
            ]
        ];
    }

    /**
     * Get fallback insights
     */
    private function getFallbackInsights()
    {
        return [
            'key_findings' => ['Unable to generate AI insights at this time'],
            'risk_analysis' => ['System maintenance in progress'],
            'recommendations' => ['Please try again later'],
            'predictions' => ['Data analysis temporarily unavailable'],
            'alerts' => ['AI service is currently offline']
        ];
    }

    /**
     * Get default summary data
     */
    private function getDefaultSummary()
    {
        return [
            'total_outstanding' => 0,
            'overdue_amount' => 0,
            'overdue_count' => 0,
            'avg_days_overdue' => 0,
            'collection_rate' => 0,
            'active_customers' => 0
        ];
    }

    /**
     * Get default aging analysis
     */
    private function getDefaultAgingAnalysis()
    {
        return [
            'current' => 0,
            'days_31_60' => 0,
            'days_61_90' => 0,
            'days_over_90' => 0
        ];
    }

    /**
     * Get start date based on period
     */
    private function getStartDate($period)
    {
        return match($period) {
            '7d' => Carbon::now()->subDays(7)->startOfDay(),
            '30d' => Carbon::now()->subDays(30)->startOfDay(),
            '90d' => Carbon::now()->subDays(90)->startOfDay(),
            '1y' => Carbon::now()->subYear()->startOfDay(),
            default => Carbon::now()->subDays(30)->startOfDay(),
        };
    }

    /**
     * Simple payment prediction
     */
    public function predictPaymentProbability($customerId)
    {
        try {
            $customerData = DB::table('consolidated_billings')
                ->where('user_id', $customerId)
                ->where('status', 'paid')
                ->select([
                    DB::raw('COUNT(*) as total_payments'),
                    DB::raw('SUM(CASE WHEN DATEDIFF(payment_date, due_date) <= 0 THEN 1 ELSE 0 END) as on_time_payments'),
                    DB::raw('AVG(DATEDIFF(payment_date, due_date)) as avg_delay_days'),
                    DB::raw('COALESCE(DATEDIFF(NOW(), MAX(payment_date)), 999) as days_since_last_payment')
                ])
                ->first();

            if (!$customerData || $customerData->total_payments == 0) {
                return [
                    'probability' => 50,
                    'risk_level' => 'medium',
                    'factors' => ['Insufficient payment history']
                ];
            }

            // Simple scoring algorithm
            $onTimeRate = ($customerData->on_time_payments / $customerData->total_payments) * 100;
            $delayPenalty = min(30, $customerData->avg_delay_days * 2);
            $recentActivity = $customerData->days_since_last_payment <= 30 ? 30 :
                            ($customerData->days_since_last_payment <= 90 ? 15 : 0);

            $score = $onTimeRate - $delayPenalty + $recentActivity;
            $probability = max(0, min(100, $score));

            return [
                'probability' => round($probability, 2),
                'risk_level' => $this->getRiskLevel($probability),
                'factors' => [
                    'on_time_rate' => round($onTimeRate, 1) . '%',
                    'avg_delay' => round($customerData->avg_delay_days, 1) . ' days',
                    'days_since_last_payment' => $customerData->days_since_last_payment
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Error predicting payment probability: ' . $e->getMessage());
            return [
                'probability' => 50,
                'risk_level' => 'unknown',
                'factors' => ['Error in calculation']
            ];
        }
    }

    /**
     * Determine risk level based on probability
     */
    private function getRiskLevel($probability)
    {
        if ($probability >= 80) return 'low';
        if ($probability >= 60) return 'medium';
        if ($probability >= 40) return 'high';
        return 'critical';
    }

    /**
     * Generate collection strategy for customer
     */
    public function generateCollectionStrategy($customerId)
    {
        $probability = $this->predictPaymentProbability($customerId);

        $strategies = [
            'low' => [
                'approach' => 'Standard payment reminder',
                'frequency' => 'Weekly follow-up',
                'communication' => 'Email reminders',
                'escalation' => 'Phone call after 2 weeks',
                'incentives' => 'None needed'
            ],
            'medium' => [
                'approach' => 'Enhanced monitoring',
                'frequency' => 'Twice weekly contact',
                'communication' => 'Email and SMS',
                'escalation' => 'Manager contact after 1 week',
                'incentives' => 'Payment plan option'
            ],
            'high' => [
                'approach' => 'Aggressive collection',
                'frequency' => 'Daily contact attempts',
                'communication' => 'Phone calls and registered mail',
                'escalation' => 'Legal notice after 3 days',
                'incentives' => 'Settlement offer'
            ],
            'critical' => [
                'approach' => 'Immediate escalation',
                'frequency' => 'Multiple contacts daily',
                'communication' => 'All channels including in-person',
                'escalation' => 'Collections agency or legal action',
                'incentives' => 'Last chance settlement'
            ]
        ];

        $riskLevel = $probability['risk_level'];

        return [
            'strategy' => $strategies[$riskLevel] ?? $strategies['medium'],
            'risk_assessment' => $probability,
            'generated_at' => now()->toDateTimeString()
        ];
    }
}
