import { signOut, showOrderHistory, showIssuedTickets } from '@/actions/App/Http/Controllers/AccountController'
import { resetPassword, organizerApplication } from '@/routes/index'
import { showUserTicket } from '@/actions/App/Http/Controllers/TicketController'
import { usePage, Link, router } from '@inertiajs/react'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Pagination } from '@/components/pagination'
import type { PaginationData } from '@/components/pagination'
import { Ticket } from '@/components/ticket'
import type { TicketData } from '@/components/ticket'
import type { AuthData } from '@/components/shared-data'
import { History, KeyRound, LogOut, Ticket as TicketIcon, Users } from 'lucide-react'

export default function Account({ tickets, isOrganizerApplicationApplied }: { tickets: PaginationData<TicketData>, isOrganizerApplicationApplied: boolean}) {
  const { auth } = usePage<AuthData>().props

  return (
    <div className="space-y-8">
      <div>
        <p className="text-sm text-muted-foreground">Welcome back</p>
        <h1 className="text-3xl font-bold tracking-tight">{auth.user.name}</h1>
      </div>

      <Card>
        <CardHeader>
          <CardTitle className="text-lg">Account Settings</CardTitle>
        </CardHeader>
        <CardContent className="flex flex-wrap gap-2">
          <Button asChild variant="outline">
            <a href={showOrderHistory().url}>
              <History className="size-4" />
              Order History
            </a>
          </Button>
          <Button asChild variant="outline">
            <a href={resetPassword().url}>
              <KeyRound className="size-4" />
              Reset Password
            </a>
          </Button>
          {!isOrganizerApplicationApplied && (
            <Button asChild variant="outline">
              <a href={organizerApplication().url}>
                <Users className="size-4" />
                Organizer Application
              </a>
            </Button>
          )}
          {auth.user.is_organizer && (
            <Button asChild variant="outline">
              <a href={showIssuedTickets().url}>
                <TicketIcon className="size-4" />
                Issued Tickets
              </a>
            </Button>
          )}
          <Button variant="destructive" onClick={() => router.post(signOut())}>
            <LogOut className="size-4" />
            Sign Out
          </Button>
        </CardContent>
      </Card>

      <section className="space-y-5">
        <h2 className="text-2xl font-bold tracking-tight">My Tickets</h2>

        {tickets.data.length === 0 ? (
          <div className="flex flex-col items-center justify-center rounded-xl border border-dashed py-16 text-center">
            <TicketIcon className="mb-3 size-10 text-muted-foreground/50" />
            <p className="font-medium text-muted-foreground">No tickets yet</p>
            <p className="mt-1 text-sm text-muted-foreground/70">Purchased tickets will appear here.</p>
          </div>
        ) : (
          <div className="grid gap-4 sm:grid-cols-2">
            {tickets.data.map((ticket) => (
              <Link key={ticket.id} href={showUserTicket(ticket.id)} className="block">
                <Ticket ticket={ticket} isEllipsis={true} className="h-full" />
              </Link>
            ))}
          </div>
        )}

        <Pagination pagination={tickets}/>
      </section>
    </div>
  )
}
