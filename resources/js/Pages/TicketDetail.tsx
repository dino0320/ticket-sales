import { Ticket } from '@/components/ticket'
import type { TicketData } from '@/components/ticket'

export default function TicketDetail({ ticket }: { ticket: TicketData}) {
  return (
    <div>
      <Ticket ticket={ticket}/>
    </div>
  )
}
