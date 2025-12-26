import { store } from '@/actions/App/Http/Controllers/CartController';
import { useState } from 'react'
import { router } from '@inertiajs/react'
import { LoadingButton } from '@/components/loading-button'
import { Counter } from '@/components/counter'
import { Ticket } from '@/components/ticket'
import type { TicketData } from '@/components/ticket'

export default function TicketDetail({ ticket }: { ticket: TicketData }) {
  const [isLoading, setIsLoading] = useState(false)
  const [numberOfTickets, setNumberOfTickets] = useState(1);

  async function onClick() {
    if (isLoading) {
      return
    }

    setIsLoading(true)
    router.post(store(ticket.id), { number_of_tickets: numberOfTickets }, {
      onSuccess: () => console.log('The thicket was added to cart'),
      onFinish: () => setIsLoading(false)
    })
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
      <LoadingButton onClick={onClick} isLoading={isLoading}>Add to cart</LoadingButton>
    </div>
  )
}
