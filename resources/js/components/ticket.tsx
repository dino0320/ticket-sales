import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'

import { formatCurrency } from '@/lib/utils'

export type TicketData = {
  id: number,
  event_title: string,
  event_description: string | null,
  price: number,
  event_start_date: string,
  event_end_date: string,
}

export type IssuedTicketData = {
  id: number,
  event_title: string,
  event_description: string | null,
  price: number,
  initial_number_of_tickets: number,
  number_of_tickets: number,
  event_start_date: string,
  event_end_date: string,
  start_date: string,
  end_date: string,
}

export function Ticket({ ticket, isEllipsis = false }: { ticket: TicketData, isEllipsis?: boolean }) {
  const eventStartDate = new Date(ticket.event_start_date)
  const eventEndDate = new Date(ticket.event_end_date)

  return (
    <Card>
      <CardHeader>
        <CardTitle>{ticket.event_title}</CardTitle>
        <CardDescription className={isEllipsis ? 'overflow-ellipsis' : ''}>
          {ticket.event_description}
        </CardDescription>
      </CardHeader>
      <CardContent>
        {formatCurrency(ticket.price)}
      </CardContent>
      <CardFooter>
        {eventStartDate.toLocaleString()} - {eventEndDate.toLocaleString()}
      </CardFooter>
    </Card>
  )
}
