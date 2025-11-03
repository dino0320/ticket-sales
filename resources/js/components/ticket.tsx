import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'

export type TicketData = {
  id: number,
  event_title: string,
  event_description: string | null,
  price: number,
  event_start_date: string,
  event_end_date: string | null,
}

export function Ticket({ ticket, isEllipsis = false }: { ticket: TicketData, isEllipsis?: boolean }) {
  const eventDate = ticket.event_start_date + (ticket.event_end_date === null ? '' : ` - ${ticket.event_end_date}`)

  return (
    <Card>
      <CardHeader>
        <CardTitle>{ticket.event_title}</CardTitle>
        <CardDescription className={isEllipsis ? 'overflow-ellipsis' : ''}>
          {ticket.event_description}
        </CardDescription>
      </CardHeader>
      <CardContent>
        ${ticket.price}
      </CardContent>
      <CardFooter>
        {eventDate}
      </CardFooter>
    </Card>
  )
}
