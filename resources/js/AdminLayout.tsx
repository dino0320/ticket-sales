import { signOut } from '@/actions/App/Http/Controllers/Admin/AccountController'
import { index } from '@/actions/App/Http/Controllers/Admin/OrganizerApplicationController'
import { usePage, Link } from '@inertiajs/react'
import type { ReactNode } from 'react'
import { ClipboardList, LogOut, Shield } from 'lucide-react'
import { cn } from '@/lib/utils'
import type { AuthData } from '@/components/shared-data'

function NavLink({ href, children }: { href: string, children: ReactNode }) {
  return (
    <Link
      href={href}
      className={cn(
        'flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-muted-foreground transition-colors hover:bg-accent hover:text-accent-foreground',
      )}
    >
      {children}
    </Link>
  )
}

export default function AdminLayout({ children }: { children: ReactNode }) {
  const { auth } = usePage<AuthData>().props

  const isSignedIn = auth.is_admin && auth.user !== null

  return (
    <div className="flex min-h-screen flex-col">
      <header className="sticky top-0 z-50 border-b bg-background/80 backdrop-blur-md">
        <div className="mx-auto flex h-16 max-w-6xl items-center justify-between px-4 sm:px-6">
          <div className="flex items-center gap-2.5 font-semibold tracking-tight">
            <span className="flex size-8 items-center justify-center rounded-lg bg-primary text-primary-foreground">
              <Shield className="size-4" />
            </span>
            <span className="text-lg">Admin</span>
          </div>

          {isSignedIn && (
            <nav className="flex items-center gap-1">
              <NavLink href={index()}>
                <ClipboardList className="size-4" />
                <span className="hidden sm:inline">Organizer Applications</span>
              </NavLink>
              <NavLink href={signOut()}>
                <LogOut className="size-4" />
                <span className="hidden sm:inline">Sign Out</span>
              </NavLink>
            </nav>
          )}
        </div>
      </header>

      <main className="flex-1">
        <div className="mx-auto max-w-6xl px-4 py-8 sm:px-6 sm:py-10">
          {children}
        </div>
      </main>
    </div>
  )
}
