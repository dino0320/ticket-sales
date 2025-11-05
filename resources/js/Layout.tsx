import { index } from "@/actions/App/Http/Controllers/HomeController";
import { Link } from '@inertiajs/react'
import type { ReactNode } from 'react'

export default function Layout({ children }: { children: ReactNode }) {
  return (
    <main>
      <header>
        <Link href={index()}>Home</Link>
        <Link href="/my-account">My Account</Link>
        <Link href="/cart">Cart</Link>
      </header>
      <article>{children}</article>
    </main>
  )
}
