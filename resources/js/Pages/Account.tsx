import { showOrderHistory,showIssuedTickets } from '@/actions/App/Http/Controllers/AccountController';
import { resetPassword, organizerApplication } from '@/routes/index';
import { showUserTicket } from '@/actions/App/Http/Controllers/TicketController';
import { usePage, Link } from '@inertiajs/react'
import { Button } from '@/components/ui/button'
import { Pagination } from '@/components/pagination'
import type { PaginationData } from '@/components/pagination'
import { Ticket } from '@/components/ticket'
import type { TicketData } from '@/components/ticket'
import type { AuthData } from '@/components/shared-data'

export default function Account({ tickets, isOrganizerApplicationApplied }: { tickets: PaginationData<TicketData>, isOrganizerApplicationApplied: boolean}) {
  const { auth } = usePage<AuthData>().props

  return (
    <div className="space-y-4">
      <section className="space-y-1">
        <h2 className="text-2xl font-bold">Account</h2>

        <div className="flex gap-1">
          <a href={showOrderHistory().url}><Button>Order history</Button></a>
          <a href={resetPassword().url}><Button variant="outline">Reset password</Button></a>
          {isOrganizerApplicationApplied ? '' : <a href={organizerApplication().url}><Button variant="outline">Organizer Application</Button></a>}
          {auth.user.is_organizer ? <a href={showIssuedTickets().url}><Button variant="outline">Issued Tickets</Button></a> : ''}
        </div>
      </section>

      <section className="space-y-1">
        <h2 className="text-2xl font-bold">My Tickets</h2>

        {tickets.data.map((ticket) => (
          <div key={ticket.id}>
            <Link href={showUserTicket(ticket.id)}>
              <Ticket ticket={ticket} isEllipsis={true}/>
            </Link>
          </div>
        ))}

        <Pagination pagination={tickets}/>
      </section>
    </div>
  )
}
