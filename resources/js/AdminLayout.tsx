import { index } from '@/actions/App/Http/Controllers/Admin/OrganizerApplicationController';
import { Link } from '@inertiajs/react'
import type { ReactNode } from 'react'
import {
  NavigationMenu,
  NavigationMenuContent,
  NavigationMenuIndicator,
  NavigationMenuItem,
  NavigationMenuLink,
  NavigationMenuList,
  NavigationMenuTrigger,
  NavigationMenuViewport,
} from '@/components/ui/navigation-menu'

export default function AdminLayout({ children }: { children: ReactNode }) {
  return (
    <main>
      <header>
        <NavigationMenu>
          <NavigationMenuList>
            <NavigationMenuItem>
              <NavigationMenuLink asChild>
                <Link href={index()}>Organizer Application</Link>
              </NavigationMenuLink>
            </NavigationMenuItem>
          </NavigationMenuList>
        </NavigationMenu>
      </header>
      <article>{children}</article>
    </main>
  )
}
