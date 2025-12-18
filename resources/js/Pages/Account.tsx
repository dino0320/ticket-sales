import { showOrderHistory } from '@/actions/App/Http/Controllers/AccountController';
import { resetPassword, organizerApplication } from '@/routes/index';
import { show } from '@/actions/App/Http/Controllers/TicketController';
import { Link } from '@inertiajs/react'
import { Button } from '@/components/ui/button'
import { Pagination } from '@/components/pagination'
import type { PaginationData } from '@/components/pagination'
import { Ticket } from '@/components/ticket'
import type { TicketData } from '@/components/ticket'

export default function Account({ tickets, isOrganizerApplicationApplied }: { tickets: PaginationData<TicketData>, isOrganizerApplicationApplied: boolean}) {
  return (
    <div>
      <a href={resetPassword().url}><Button>Reset password</Button></a>
      <a href={showOrderHistory().url}><Button>Order history</Button></a>
      {isOrganizerApplicationApplied ? '' : <a href={organizerApplication().url}><Button>Organizer Application</Button></a>}

      {tickets.data.map((ticket) => (
        <Link key={ticket.id} href={show(ticket.id)}>
          <Ticket ticket={ticket} isEllipsis={true}/>
        </Link>
      ))}

      <Pagination pagination={tickets}/>
    </div>
  )
}
