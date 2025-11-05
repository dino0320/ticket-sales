import { store } from "@/actions/App/Http/Controllers/UserCartController";
import { router } from '@inertiajs/react'
import { Button } from '@/components/ui/button'
import { Ticket } from '@/components/ticket'
import type { TicketData } from '@/components/ticket'

export default function TicketDetail({ ticket }: { ticket: TicketData}) {
  async function onClick() {
      try {
        router.post(store(), { id: ticket.id, number_of_tickets: 1 }, { 
          onSuccess: () => console.log('The thicket was added to cart'),
          onError: () => console.error('Can\'t add to cart'),
        })
      } catch (error) {
        console.error('Can\'t add to cart', error)
      }
    }

  return (
    <div>
      <Ticket ticket={ticket}/>

      <Button onClick={onClick}>Add to cart</Button>
    </div>
  )
}
