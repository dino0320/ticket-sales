import { checkout } from '@/actions/App/Http/Controllers/CheckoutController'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card'
import { Pagination } from '@/components/pagination'
import type { PaginationData } from '@/components/pagination'
import { Ticket } from '@/components/ticket'
import type { TicketData } from '@/components/ticket'
import { formatCurrency } from '@/lib/utils'
import { ArrowRight, ClipboardList } from 'lucide-react'

export default function Review({ tickets, numbersOfTickets, totalPriceOfTickets }: { tickets: PaginationData<TicketData>, numbersOfTickets: {[id: number]: number}, totalPriceOfTickets: number}) {
  if (tickets.data.length === 0) {
    return (
      <div className="flex flex-col items-center justify-center rounded-xl border border-dashed py-20 text-center">
        <ClipboardList className="mb-3 size-10 text-muted-foreground/50" />
        <p className="font-medium text-muted-foreground">Nothing to review</p>
        <p className="mt-1 text-sm text-muted-foreground/70">Add tickets to your cart first.</p>
      </div>
    )
  }

  return (
    <div className="space-y-6">
      <h1 className="text-2xl font-bold tracking-tight">Review Order</h1>

      <div className="grid gap-6 lg:grid-cols-3">
        <div className="space-y-4 lg:col-span-2">
          {tickets.data.map((ticket) => (
            <div key={ticket.id} className="relative rounded-xl border p-4">
              <Ticket ticket={ticket} isEllipsis={true} className="border-0 shadow-none" />
              <Badge className="absolute top-6 right-6" variant="secondary">
                × {numbersOfTickets[ticket.id]}
              </Badge>
            </div>
          ))}

          <Pagination pagination={tickets}/>
        </div>

        <div className="lg:col-span-1">
          <Card className="sticky top-24">
            <CardHeader>
              <CardTitle>Order Total</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="flex items-center justify-between text-sm">
                <span className="text-muted-foreground">Total</span>
                <span className="text-2xl font-bold text-primary">{formatCurrency(totalPriceOfTickets)}</span>
              </div>
            </CardContent>
            <CardFooter>
              <a href={checkout().url} className="w-full">
                <Button className="w-full" size="lg">
                  Proceed to Checkout
                  <ArrowRight className="size-4" />
                </Button>
              </a>
            </CardFooter>
          </Card>
        </div>
      </div>
    </div>
  )
}
