import { show } from '@/actions/App/Http/Controllers/TicketController';
import { updateNumberOfTickets } from '@/actions/App/Http/Controllers/UserCartController';
import { useState } from 'react'
import { Link } from '@inertiajs/react'
import { router } from '@inertiajs/react'
import { Button } from '@/components/ui/button'
import { Counter } from '@/components/counter'
import { Pagination } from '@/components/pagination'
import type { PaginationData } from '@/components/pagination'
import { Ticket } from '@/components/ticket'
import type { TicketData } from '@/components/ticket'

type NumberOfTickets = {[id: number]: number}

export default function Cart({ tickets, numberOfTickets }: { tickets: PaginationData<TicketData>, numberOfTickets: NumberOfTickets}) {
  const [numberOfTicketsById, setNumberOfTicketsById] = useState<NumberOfTickets>(numberOfTickets)

  async function onClick() {
    
  }

  async function updateNumber(number: number, ticketId: number) {
    try {
      if (number < 1) {
        return
      }

      router.post(updateNumberOfTickets(ticketId), { number_of_tickets: number }, { 
        onSuccess: () => {
          setNumberOfTicketsById(prev => ({...prev, [ticketId]: number}))
        },
        onError: () => console.error('Can\'t update the number of thickets'),
      })
    } catch (error) {
      console.error('Can\'t update the number of thickets', error)
    }
  }

  return (
    <div>
      <Button onClick={onClick}>Proceed to checkout</Button>

      {tickets.data.map((ticket) => (
        <div key={ticket.id}>
          <Link href={show(ticket.id)}>
            <Ticket ticket={ticket} isEllipsis={true}/>
          </Link>
          <Counter number={numberOfTicketsById[ticket.id] ?? 0} ticketId={ticket.id} updateNumber={updateNumber}/>
        </div>
      ))}

      <Pagination pagination={tickets}/>
    </div>
  )
}
