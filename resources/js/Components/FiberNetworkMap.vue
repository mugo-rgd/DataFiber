<template>
  <div class="fiber-map-container">
    <div class="map-controls">
      <select v-model="selectedRegion" @change="filterByRegion">
        <option value="">All Regions</option>
        <option v-for="region in regions" :key="region" :value="region">
          {{ region }}
        </option>
      </select>

      <select v-model="selectedLinkType" @change="filterByLinkType">
        <option value="">All Link Types</option>
        <option value="Metro">Metro</option>
        <option value="Premium">Premium</option>
        <option value="Non Premium">Non Premium</option>
      </select>

      <select v-model="selectedStatus" @change="filterByStatus">
        <option value="">All Status</option>
        <option value="Active">Active</option>
        <option value="Damaged">Damaged</option>
        <option value="Planned">Planned</option>
      </select>

      <button @click="refreshData" class="btn-refresh">
        <i class="fas fa-sync-alt"></i> Refresh
      </button>
    </div>

    <div class="stats-panel" v-if="stats">
      <div class="stat-item">
        <span class="stat-label">Total Networks:</span>
        <span class="stat-value">{{ stats.total_networks }}</span>
      </div>
      <div class="stat-item">
        <span class="stat-label">Total Distance:</span>
        <span class="stat-value">{{ stats.total_distance.toFixed(2) }} km</span>
      </div>
      <div class="stat-item">
        <span class="stat-label">Monthly Revenue:</span>
        <span class="stat-value">KES {{ formatNumber(stats.total_monthly_revenue) }}</span>
      </div>
    </div>

    <div id="map" class="map"></div>

    <!-- Network Info Popup -->
    <div v-if="selectedNetwork" class="info-panel">
      <h3>{{ selectedNetwork.network_name }}</h3>
      <div class="info-content">
        <p><strong>Network ID:</strong> {{ selectedNetwork.network_id }}</p>
        <p><strong>Region:</strong> {{ selectedNetwork.region }}</p>
        <p><strong>Distance:</strong> {{ selectedNetwork.total_distance_km }} km</p>
        <p><strong>Fiber Cores:</strong> {{ selectedNetwork.fiber_cores }}</p>
        <p><strong>Link Type:</strong>
          <span :class="'badge-' + selectedNetwork.link_type.toLowerCase().replace(' ', '-')">
            {{ selectedNetwork.link_type }}
          </span>
        </p>
        <p><strong>Monthly Cost:</strong> {{ selectedNetwork.currency }} {{ formatNumber(selectedNetwork.cost_per_month) }}</p>
        <p><strong>Status:</strong>
          <span :class="'status-' + selectedNetwork.status.toLowerCase()">
            {{ selectedNetwork.status }}
          </span>
        </p>
        <p><strong>Route:</strong> {{ selectedNetwork.connection_sequence }}</p>
      </div>
      <button @click="closeInfoPanel" class="btn-close">×</button>
    </div>
  </div>
</template>

<script>
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import axios from 'axios';

export default {
  name: 'FiberNetworkMap',

  data() {
    return {
      map: null,
      layers: {
        networks: null,
        nodes: null
      },
      networks: [],
      nodes: [],
      regions: [],
      stats: null,
      selectedRegion: '',
      selectedLinkType: '',
      selectedStatus: '',
      selectedNetwork: null,
      markers: []
    }
  },

  mounted() {
    this.initMap();
    this.loadData();
    this.loadStats();
  },

  methods: {
    initMap() {
      // Initialize map centered on Kenya
      this.map = L.map('map').setView([-1.286389, 36.817223], 7);

      // Add tile layer
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
      }).addTo(this.map);

      // Initialize feature layers
      this.layers.networks = L.layerGroup().addTo(this.map);
      this.layers.nodes = L.layerGroup().addTo(this.map);

      // Fix marker icons
      delete L.Icon.Default.prototype._getIconUrl;
      L.Icon.Default.mergeOptions({
        iconRetinaUrl: require('leaflet/dist/images/marker-icon-2x.png'),
        iconUrl: require('leaflet/dist/images/marker-icon.png'),
        shadowUrl: require('leaflet/dist/images/marker-shadow.png'),
      });
    },

    async loadData() {
      try {
        // Load GeoJSON data
        const response = await axios.get('/api/networks/geojson');
        this.networks = response.data.features;

        // Load nodes
        const nodesResponse = await axios.get('/api/nodes');
        this.nodes = nodesResponse.data;

        // Extract unique regions
        this.regions = [...new Set(this.nodes.map(n => n.region))];

        this.renderMap();
      } catch (error) {
        console.error('Error loading data:', error);
      }
    },

    async loadStats() {
      try {
        const response = await axios.get('/api/dashboard/stats');
        this.stats = response.data;
      } catch (error) {
        console.error('Error loading stats:', error);
      }
    },

    renderMap() {
      // Clear existing layers
      this.layers.networks.clearLayers();
      this.layers.nodes.clearLayers();

      // Filter networks
      let filteredNetworks = this.networks;

      if (this.selectedRegion) {
        filteredNetworks = filteredNetworks.filter(n =>
          n.properties.region === this.selectedRegion
        );
      }

      if (this.selectedLinkType) {
        filteredNetworks = filteredNetworks.filter(n =>
          n.properties.link_type === this.selectedLinkType
        );
      }

      if (this.selectedStatus) {
        filteredNetworks = filteredNetworks.filter(n =>
          n.properties.status === this.selectedStatus
        );
      }

      // Render networks
      filteredNetworks.forEach(network => {
        const color = this.getNetworkColor(network.properties);
        const weight = this.getNetworkWeight(network.properties);

        const polyline = L.polyline(network.geometry.coordinates.map(coord => [coord[1], coord[0]]), {
          color: color,
          weight: weight,
          opacity: 0.8
        });

        polyline.on('click', () => {
          this.showNetworkInfo(network.properties);
        });

        polyline.bindTooltip(network.properties.name, {
          permanent: false,
          direction: 'center'
        });

        polyline.addTo(this.layers.networks);
      });

      // Render nodes (filtered by region)
      const filteredNodes = this.selectedRegion
        ? this.nodes.filter(n => n.region === this.selectedRegion)
        : this.nodes;

      filteredNodes.forEach(node => {
        const marker = L.marker([node.latitude, node.longitude], {
          icon: this.getNodeIcon(node.node_type)
        });

        marker.bindPopup(`
          <b>${node.node_name}</b><br>
          Type: ${node.node_type}<br>
          Region: ${node.region}
        `);

        marker.addTo(this.layers.nodes);
        this.markers.push(marker);
      });
    },

    getNetworkColor(properties) {
      if (properties.status === 'Damaged') return '#FF0000';

      switch(properties.link_type) {
        case 'Premium': return '#FFA500';
        case 'Metro': return '#00FF00';
        case 'Non Premium': return '#FFFF00';
        default: return '#0000FF';
      }
    },

    getNetworkWeight(properties) {
      return Math.min(8, Math.max(3, Math.floor(properties.fiber_cores / 12)));
    },

    getNodeIcon(nodeType) {
      const iconUrl = nodeType === 'SS'
        ? '/images/substation-icon.png'
        : nodeType === 'OFFICE'
          ? '/images/office-icon.png'
          : '/images/node-icon.png';

      return L.icon({
        iconUrl: iconUrl,
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34]
      });
    },

    showNetworkInfo(network) {
      this.selectedNetwork = network;
    },

    closeInfoPanel() {
      this.selectedNetwork = null;
    },

    filterByRegion() {
      this.renderMap();
    },

    filterByLinkType() {
      this.renderMap();
    },

    filterByStatus() {
      this.renderMap();
    },

    refreshData() {
      this.loadData();
      this.loadStats();
    },

    formatNumber(num) {
      return new Intl.NumberFormat('en-KE').format(num);
    }
  }
}
</script>

<style scoped>
.fiber-map-container {
  position: relative;
  width: 100%;
  height: 100vh;
}

.map-controls {
  position: absolute;
  top: 20px;
  left: 20px;
  z-index: 1000;
  background: white;
  padding: 15px;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  display: flex;
  gap: 10px;
}

.map-controls select {
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  min-width: 150px;
}

.btn-refresh {
  padding: 8px 16px;
  background: #4CAF50;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.btn-refresh:hover {
  background: #45a049;
}

.stats-panel {
  position: absolute;
  top: 20px;
  right: 20px;
  z-index: 1000;
  background: white;
  padding: 15px;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  min-width: 250px;
}

.stat-item {
  display: flex;
  justify-content: space-between;
  margin-bottom: 8px;
  padding: 5px 0;
  border-bottom: 1px solid #eee;
}

.stat-label {
  font-weight: bold;
  color: #555;
}

.stat-value {
  color: #333;
}

.map {
  width: 100%;
  height: 100%;
}

.info-panel {
  position: absolute;
  bottom: 30px;
  left: 30px;
  z-index: 1000;
  background: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 20px rgba(0,0,0,0.2);
  max-width: 400px;
  animation: slideIn 0.3s ease;
}

@keyframes slideIn {
  from {
    transform: translateY(100px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

.info-panel h3 {
  margin-top: 0;
  color: #333;
  border-bottom: 2px solid #4CAF50;
  padding-bottom: 10px;
}

.info-content p {
  margin: 8px 0;
  line-height: 1.5;
}

.badge-metro {
  background: #00FF00;
  color: #000;
  padding: 3px 8px;
  border-radius: 4px;
  font-weight: bold;
}

.badge-premium {
  background: #FFA500;
  color: #fff;
  padding: 3px 8px;
  border-radius: 4px;
  font-weight: bold;
}

.badge-non-premium {
  background: #FFFF00;
  color: #000;
  padding: 3px 8px;
  border-radius: 4px;
  font-weight: bold;
}

.status-active {
  color: #4CAF50;
  font-weight: bold;
}

.status-damaged {
  color: #FF0000;
  font-weight: bold;
}

.btn-close {
  position: absolute;
  top: 10px;
  right: 10px;
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
  color: #999;
}

.btn-close:hover {
  color: #333;
}
</style>
