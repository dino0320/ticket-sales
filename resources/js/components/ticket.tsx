import {
  Card,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { CalendarDays } from 'lucide-react'

import { cn, formatCurrency, formatEventDate } from '@/lib/utils'

export type TicketData = {
  id: number,
  event_title: string,
  event_description: string | null,
  price: number,
  number_of_tickets: number,
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

export function Ticket({ ticket, isEllipsis = false, className }: { ticket: TicketData, isEllipsis?: boolean, className?: string }) {
  const isSoldOut = ticket.number_of_tickets === 0
  const isLowStock = !isSoldOut && ticket.number_of_tickets <= 10

  return (
    <Card className={cn(
      'group transition-all duration-200 hover:border-primary/30 hover:shadow-md',
      className,
    )}>
      <CardHeader className="pb-3">
        <div className="flex items-start justify-between gap-3">
          <CardTitle className="text-lg leading-snug">{ticket.event_title}</CardTitle>
          <div className="flex shrink-0 flex-col items-end gap-1.5">
            <span className="text-lg font-bold text-primary">{formatCurrency(ticket.price)}</span>
            {isSoldOut ? (
              <Badge variant="destructive">Sold Out</Badge>
            ) : isLowStock ? (
              <Badge variant="secondary">{ticket.number_of_tickets} left</Badge>
            ) : (
              <Badge variant="outline" className="text-muted-foreground">Available</Badge>
            )}
          </div>
        </div>
        {ticket.event_description && (
          <CardDescription className={cn('line-clamp-2', isEllipsis && 'overflow-ellipsis')}>
            {ticket.event_description}
          </CardDescription>
        )}
      </CardHeader>
      <CardFooter className="border-t pt-4 text-sm text-muted-foreground">
        <div className="flex items-center gap-2">
          <CalendarDays className="size-4 shrink-0 text-primary/70" />
          <span>{formatEventDate(ticket.event_start_date, ticket.event_end_date)}</span>
        </div>
      </CardFooter>
    </Card>
  )
}
