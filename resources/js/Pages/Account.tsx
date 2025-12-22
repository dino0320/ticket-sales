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
    <div>
      <a href={resetPassword().url}><Button>Reset password</Button></a>
      <a href={showOrderHistory().url}><Button>Order history</Button></a>
      {isOrganizerApplicationApplied ? '' : <a href={organizerApplication().url}><Button>Organizer Application</Button></a>}
      {auth.user.is_organizer ? <a href={showIssuedTickets().url}><Button>Issued Tickets</Button></a> : ''}

      {tickets.data.map((ticket) => (
        <Link key={ticket.id} href={showUserTicket(ticket.id)}>
          <Ticket ticket={ticket} isEllipsis={true}/>
        </Link>
      ))}

      <Pagination pagination={tickets}/>
    </div>
  )
}
