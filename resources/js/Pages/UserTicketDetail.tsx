import { Ticket } from '@/components/ticket'
import type { TicketData } from '@/components/ticket'
import {QRCodeSVG} from 'qrcode.react';

export default function UserTicketDetail({ ticket, ticket_use_url }: { ticket: TicketData, ticket_use_url: string }) {
  return (
    <div className="space-y-4">
      <section className="space-y-1">
        <h2 className="text-2xl font-bold">Ticket Details</h2>

        <Ticket ticket={ticket}/>
      </section>

      <section className="space-y-1">
        <h2 className="text-2xl font-bold">QR Code</h2>

        <p className="text-muted-foreground text-sm">
          This QR code will expire in 10 minutes.
        </p>

        <div className="p-4">
          <QRCodeSVG value={ticket_use_url} />
        </div>
      </section>
    </div>
  )
}
