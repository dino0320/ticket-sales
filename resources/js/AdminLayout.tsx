import { signOut } from '@/actions/App/Http/Controllers/Admin/AccountController'
import { index } from '@/actions/App/Http/Controllers/Admin/OrganizerApplicationController'
import { usePage, Link } from '@inertiajs/react'
import type { ReactNode } from 'react'
import {
  NavigationMenu,
  NavigationMenuItem,
  NavigationMenuLink,
  NavigationMenuList,
} from '@/components/ui/navigation-menu'
import type { AuthData } from '@/components/shared-data'

export default function AdminLayout({ children }: { children: ReactNode }) {
  const { auth } = usePage<AuthData>().props

  const isSignedIn = auth.is_admin && auth.user !== null
  const organizerApplicationMenuItem = isSignedIn ? (
    <NavigationMenuItem>
      <NavigationMenuLink asChild>
        <Link href={index()}>Organizer Application</Link>
      </NavigationMenuLink>
    </NavigationMenuItem>
  ) : null

  const signOutMenuItem = isSignedIn ? (
    <NavigationMenuItem>
      <NavigationMenuLink asChild>
        <Link href={signOut()}>Sign Out</Link>
      </NavigationMenuLink>
    </NavigationMenuItem>
  ) : null

  return (
    <main>
      <header>
        <NavigationMenu>
          <NavigationMenuList>
            {organizerApplicationMenuItem}
            {signOutMenuItem}
          </NavigationMenuList>
        </NavigationMenu>
      </header>
      <article>{children}</article>
    </main>
  )
}
