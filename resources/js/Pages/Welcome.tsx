//import Layout from './Layout'
import { Head } from '@inertiajs/react'

export default function Welcome({ username }) {
  return (
    <div>
      <Head title="Welcome" />
      <h1>Welcome</h1>
      <p>Hello {username}, welcome to your first Inertia app!</p>
    </div>
  )
}
