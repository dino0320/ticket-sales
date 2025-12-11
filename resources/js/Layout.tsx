import { index } from '@/actions/App/Http/Controllers/HomeController';
import { Link } from '@inertiajs/react'
import type { ReactNode } from 'react'
import { MdAccountCircle } from 'react-icons/md';
import { FaCartShopping } from 'react-icons/fa6';

export default function Layout({ children }: { children: ReactNode }) {
  return (
    <main>
      <header>
        <Link href={index()}>Home</Link>
        <Link href="my-account"><MdAccountCircle />My Account</Link>
        <Link href="/cart"><FaCartShopping />Cart</Link>
      </header>
      <article>{children}</article>
    </main>
  )
}
