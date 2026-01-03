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
  
  const organizerApplication = auth.user === null ? null : (
    <NavigationMenuItem>
      <NavigationMenuLink asChild>
        <Link href={index()}>Organizer Application</Link>
      </NavigationMenuLink>
    </NavigationMenuItem>
  )

  return (
    <main>
      <header>
        <NavigationMenu>
          <NavigationMenuList>
            {organizerApplication}
          </NavigationMenuList>
        </NavigationMenu>
      </header>
      <article>{children}</article>
    </main>
  )
}
