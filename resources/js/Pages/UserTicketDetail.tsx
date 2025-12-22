import { Ticket } from '@/components/ticket'
import type { TicketData } from '@/components/ticket'
import {QRCodeSVG} from 'qrcode.react';

export default function UserTicketDetail({ ticket, ticket_use_url }: { ticket: TicketData, ticket_use_url: string }) {
  return (
    <div>
      <Ticket ticket={ticket}/>
      <QRCodeSVG value={ticket_use_url} />
    </div>
  )
}
