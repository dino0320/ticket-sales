import { checkout } from '@/actions/App/Http/Controllers/CheckoutController';
import { router } from '@inertiajs/react'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Pagination } from '@/components/pagination'
import type { PaginationData } from '@/components/pagination'
import { Ticket } from '@/components/ticket'
import type { TicketData } from '@/components/ticket'

export default function Checkout({ tickets, numberOfTickets, totalPriceOfTickets }: { tickets: PaginationData<TicketData>, numberOfTickets: {[id: number]: number}, totalPriceOfTickets: number}) {
  /*async function onClick() {
    try {
      router.post(checkout())
    } catch (error) {
      console.error('Can\'t go to checkout', error)
    }
  }*/

  return (
    <div>
      <a href={checkout().url}><Button>Proceed to checkout</Button></a>
      <p>${totalPriceOfTickets}</p>

      {tickets.data.map((ticket) => (
        <div key={ticket.id}>
          <Ticket ticket={ticket} isEllipsis={true}/>
          <Badge>{numberOfTickets[ticket.id]}</Badge>
        </div>
      ))}

      <Pagination pagination={tickets}/>
    </div>
  )
}
