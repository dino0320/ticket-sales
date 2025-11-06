import { show } from '@/actions/App/Http/Controllers/TicketController';
import { Link } from '@inertiajs/react'
import { Badge } from '@/components/ui/badge'
import { Pagination } from '@/components/pagination'
import type { PaginationData } from '@/components/pagination'
import { Ticket } from '@/components/ticket'
import type { TicketData } from '@/components/ticket'

export default function Cart({ tickets, numberOfTickets }: { tickets: PaginationData<TicketData>, numberOfTickets: {[id: number]: number}}) {
  return (
    <div>
      {tickets.data.map((ticket) => (
        <Link key={ticket.id} href={show(ticket.id)}>
          <Ticket ticket={ticket} isEllipsis={true}/>
          <Badge>{numberOfTickets[ticket.id]}</Badge>
        </Link>
      ))}

      <Pagination pagination={tickets}/>
    </div>
  )
}
