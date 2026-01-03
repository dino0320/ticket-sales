import { checkout } from '@/actions/App/Http/Controllers/CheckoutController'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Pagination } from '@/components/pagination'
import type { PaginationData } from '@/components/pagination'
import { Ticket } from '@/components/ticket'
import type { TicketData } from '@/components/ticket'
import { formatCurrency } from '@/lib/utils'

export default function Review({ tickets, numberOfTickets, totalPriceOfTickets }: { tickets: PaginationData<TicketData>, numberOfTickets: {[id: number]: number}, totalPriceOfTickets: number}) {
  return (
    <div className="space-y-1">
      {tickets.data.length === 0 ? (
        <p>No items in your cart.</p>
      ) : (
        <div>
          <a href={checkout().url}><Button>Proceed to checkout</Button></a>
          <p>{formatCurrency(totalPriceOfTickets)}</p>
        </div>
      )}

      {tickets.data.map((ticket) => (
        <div key={ticket.id}>
          <Ticket ticket={ticket} isEllipsis={true}/>
          <Badge className="text-sm">{numberOfTickets[ticket.id]}</Badge>
        </div>
      ))}

      <Pagination pagination={tickets}/>
    </div>
  )
}
