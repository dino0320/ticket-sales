import { store } from '@/actions/App/Http/Controllers/CartController';
import { useState } from 'react'
import { router } from '@inertiajs/react'
import { Button } from '@/components/ui/button'
import { Counter } from '@/components/counter'
import { Ticket } from '@/components/ticket'
import type { TicketData } from '@/components/ticket'

export default function TicketDetail({ ticket }: { ticket: TicketData }) {
  const [numberOfTickets, setNumberOfTickets] = useState(1);

  async function onClick() {
    try {
      router.post(store(ticket.id), { number_of_tickets: numberOfTickets }, { 
        onSuccess: () => console.log('The thicket was added to cart'),
        onError: () => console.error('Can\'t add to cart'),
      })
    } catch (error) {
      console.error('Can\'t add to cart', error)
    }
  }

  async function updateNumber(number: number) {
    if (number < 1) {
      return
    }

    setNumberOfTickets(number)
  }

  return (
    <div>
      <Ticket ticket={ticket}/>
      <Counter number={numberOfTickets} ticketId={ticket.id} updateNumber={updateNumber}/>
      <Button onClick={onClick}>Add to cart</Button>
    </div>
  )
}
