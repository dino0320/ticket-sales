import { Ticket } from '@/components/ticket'
import type { TicketData } from '@/components/ticket'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { QRCodeSVG } from 'qrcode.react'
import { QrCode } from 'lucide-react'

export default function UserTicketDetail({ ticket, ticket_use_url }: { ticket: TicketData, ticket_use_url: string }) {
  return (
    <div className="mx-auto max-w-lg space-y-6">
      <Ticket ticket={ticket} />

      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2 text-lg">
            <QrCode className="size-5 text-primary" />
            Entry QR Code
          </CardTitle>
        </CardHeader>
        <CardContent className="flex flex-col items-center gap-4">
          <p className="text-center text-sm text-muted-foreground">
            Show this QR code at the venue entrance. It expires in 10 minutes.
          </p>
          <div className="rounded-xl border bg-white p-6">
            <QRCodeSVG value={ticket_use_url} size={200} />
          </div>
        </CardContent>
      </Card>
    </div>
  )
}
