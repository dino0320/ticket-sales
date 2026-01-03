import { show } from '@/actions/App/Http/Controllers/TicketController'
import { Link } from '@inertiajs/react'
import { Pagination } from '@/components/pagination'
import type { PaginationData } from '@/components/pagination'
import { Ticket } from '@/components/ticket'
import type { TicketData } from '@/components/ticket'

export default function Home({ tickets }: { tickets: PaginationData<TicketData>}) {
  return (
    <div className="space-y-1">
      <h2 className="text-2xl font-bold">Tickets</h2>

      {tickets.data.map((ticket) => (
        <div key={ticket.id}>
          <Link href={show(ticket.id)}>
            <Ticket ticket={ticket} isEllipsis={true}/>
          </Link>
        </div>
      ))}

      <Pagination pagination={tickets}/>
    </div>
  )
}
