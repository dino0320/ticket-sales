import { store } from '@/actions/App/Http/Controllers/CartController'
import { useEffect, useState } from 'react'
import { router } from '@inertiajs/react'
import { toast } from 'sonner'
import { LoadingButton } from '@/components/loading-button'
import { Counter } from '@/components/counter'
import { Ticket } from '@/components/ticket'
import type { TicketData } from '@/components/ticket'

export default function TicketDetail({ ticket, errors }: { ticket: TicketData, errors: Record<string, string> }) {
  const [isLoading, setIsLoading] = useState(false)
  const [numberOfTickets, setNumberOfTickets] = useState(1)

  const isOnSale = ticket.number_of_tickets !== 0;

  useEffect(() => {
    if (errors.sales_period !== undefined) {
      toast.error(errors.sales_period)
    }
    if (errors.number_of_tickets !== undefined) {
      toast.error(errors.number_of_tickets)
    }
  }, [errors]);

  async function onClick() {
    if (isLoading) {
      return
    }

    setIsLoading(true)
    router.post(store(ticket.id), { number_of_tickets: numberOfTickets }, {
      onSuccess: () => toast.success('The ticket has been added to your cart'),
      onFinish: () => setIsLoading(false)
    })
  }

  async function updateNumber(number: number) {
    if (number <= 0) {
      return
    }

    setNumberOfTickets(number)
  }

  return (
    <div>
      <Ticket ticket={ticket}/>
      <Counter number={numberOfTickets} ticketId={ticket.id} updateNumber={updateNumber}/>
      <LoadingButton onClick={onClick} isLoading={isLoading} disabled={!isOnSale}>{isOnSale ? "Add to cart" : "Sold Out"}</LoadingButton>
    </div>
  )
}
