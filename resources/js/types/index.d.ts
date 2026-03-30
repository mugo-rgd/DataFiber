import { PageProps as InertiaPageProps } from '@inertiajs/core'

export interface User {
  id: number
  name: string
  email: string
}

export interface PageProps extends InertiaPageProps {
  auth: {
    user: User | null
  }
}
