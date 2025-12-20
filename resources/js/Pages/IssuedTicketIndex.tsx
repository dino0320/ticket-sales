import { showIssuedTicket } from '@/actions/App/Http/Controllers/TicketController';
import { Link } from '@inertiajs/react'
import { Pagination } from '@/components/pagination'
import type { PaginationData } from '@/components/pagination'
import { Ticket } from '@/components/ticket'
import type { TicketData } from '@/components/ticket'

export default function IssuedTicketIndex({ tickets }: { tickets: PaginationData<TicketData>}) {
  return (
    <div>
      {tickets.data.map((ticket) => (
        <Link key={ticket.id} href={showIssuedTicket(ticket.id)}>
          <Ticket ticket={ticket} isEllipsis={true}/>
        </Link>
      ))}

      <Pagination pagination={tickets}/>
    </div>
  )
}
