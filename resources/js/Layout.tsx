import { index } from '@/actions/App/Http/Controllers/HomeController'
import { show as showAccount } from '@/actions/App/Http/Controllers/AccountController'
import { show as showCart } from '@/actions/App/Http/Controllers/CartController'
import { usePage, Link } from '@inertiajs/react'
import type { ReactNode } from 'react'
import { IoMdHome } from 'react-icons/io'
import { MdAccountCircle } from 'react-icons/md'
import { FaCartShopping } from 'react-icons/fa6'
import {
  NavigationMenu,
  NavigationMenuItem,
  NavigationMenuLink,
  NavigationMenuList,
} from '@/components/ui/navigation-menu'
import { Toaster } from '@/components/ui/sonner'
import type { AuthData } from '@/components/shared-data'

export default function Layout({ children }: { children: ReactNode }) {
  const { auth } = usePage<AuthData>().props

  const myAccount = auth.user === null ? null : (
    <NavigationMenuItem>
      <NavigationMenuLink asChild>
        <Link href={showAccount()} className="flex items-center text-xs"><MdAccountCircle className="size-6" />My Account</Link>
      </NavigationMenuLink>
    </NavigationMenuItem>
  )

  return (
    <main>
      <header>
        <NavigationMenu>
          <NavigationMenuList>
            <NavigationMenuItem>
              <NavigationMenuLink asChild>
                <Link href={index()} className="flex items-center text-xs"><IoMdHome className="size-6" />Home</Link>
              </NavigationMenuLink>
            </NavigationMenuItem>
            {myAccount}
            <NavigationMenuItem>
              <NavigationMenuLink asChild>
                <Link href={showCart()} className="flex items-center text-xs"><FaCartShopping className="size-6" />Cart</Link>
              </NavigationMenuLink>
            </NavigationMenuItem>
          </NavigationMenuList>
        </NavigationMenu>
      </header>
      <article>{children}</article>
      <Toaster />
    </main>
  )
}
