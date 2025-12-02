import { showOrderHistory } from '@/actions/App/Http/Controllers/AccountController';
import { show } from '@/actions/App/Http/Controllers/TicketController';
import { Link } from '@inertiajs/react'
import { Button } from '@/components/ui/button'
import { Pagination } from '@/components/pagination'
import type { PaginationData } from '@/components/pagination'
import { Ticket } from '@/components/ticket'
import type { TicketData } from '@/components/ticket'

export default function Account({ tickets }: { tickets: PaginationData<TicketData>}) {
  return (
    <div>
      <a href={showOrderHistory().url}><Button>Order history</Button></a>

      {tickets.data.map((ticket) => (
        <Link key={ticket.id} href={show(ticket.id)}>
          <Ticket ticket={ticket} isEllipsis={true}/>
        </Link>
      ))}

      <Pagination pagination={tickets}/>
    </div>
  )
}
