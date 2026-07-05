import { index } from '@/actions/App/Http/Controllers/HomeController'
import { show as showAccount } from '@/actions/App/Http/Controllers/AccountController'
import { show as showCart } from '@/actions/App/Http/Controllers/CartController'
import { signIn } from '@/routes/index'
import { usePage, Link } from '@inertiajs/react'
import type { ReactNode } from 'react'
import { Home, ShoppingCart, User, Ticket } from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Toaster } from '@/components/ui/sonner'
import { cn } from '@/lib/utils'
import type { AuthData } from '@/components/shared-data'

function NavLink({ href, children, className }: { href: string, children: ReactNode, className?: string }) {
  return (
    <Link
      href={href}
      className={cn(
        'flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-muted-foreground transition-colors hover:bg-accent hover:text-accent-foreground',
        className,
      )}
    >
      {children}
    </Link>
  )
}

export default function Layout({ children }: { children: ReactNode }) {
  const { auth } = usePage<AuthData>().props

  return (
    <div className="flex min-h-screen flex-col">
      <header className="sticky top-0 z-50 border-b bg-background/80 backdrop-blur-md">
        <div className="mx-auto flex h-16 max-w-6xl items-center justify-between px-4 sm:px-6">
          <Link href={index()} className="flex items-center gap-2.5 font-semibold tracking-tight">
            <span className="flex size-8 items-center justify-center rounded-lg bg-primary text-primary-foreground">
              <Ticket className="size-4" />
            </span>
            <span className="text-lg">Ticket Sales</span>
          </Link>

          <nav className="flex items-center gap-1">
            <NavLink href={index()}>
              <Home className="size-4" />
              <span className="hidden sm:inline">Home</span>
            </NavLink>
            <NavLink href={showCart()}>
              <ShoppingCart className="size-4" />
              <span className="hidden sm:inline">Cart</span>
            </NavLink>
            {auth.user !== null ? (
              <NavLink href={showAccount()}>
                <User className="size-4" />
                <span className="hidden sm:inline">Account</span>
              </NavLink>
            ) : (
              <Button asChild size="sm" className="ml-1">
                <Link href={signIn()}>Sign In</Link>
              </Button>
            )}
          </nav>
        </div>
      </header>

      <main className="flex-1">
        <div className="mx-auto max-w-6xl px-4 py-8 sm:px-6 sm:py-10">
          {children}
        </div>
      </main>

      <footer className="border-t bg-muted/40">
        <div className="mx-auto max-w-6xl px-4 py-6 sm:px-6">
          <p className="text-center text-sm text-muted-foreground">
            &copy; {new Date().getFullYear()} Ticket Sales. All rights reserved.
          </p>
        </div>
      </footer>

      <Toaster />
    </div>
  )
}
