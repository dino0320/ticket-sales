import { show as showTichet } from '@/actions/App/Http/Controllers/TicketController'
import { update, destroy } from '@/actions/App/Http/Controllers/CartController'
import { show as showReview } from '@/actions/App/Http/Controllers/CheckoutController'
import { useState, useEffect } from 'react'
import { Link, router } from '@inertiajs/react'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card'
import { toast } from 'sonner'
import { Counter } from '@/components/counter'
import { Pagination } from '@/components/pagination'
import type { PaginationData } from '@/components/pagination'
import { Ticket } from '@/components/ticket'
import type { TicketData } from '@/components/ticket'
import { formatCurrency } from '@/lib/utils'
import { ArrowRight, ShoppingCart, Trash2 } from 'lucide-react'
import axios from 'axios'

type NumbersOfTickets = {[id: number]: number}

export default function Cart({ tickets, numbersOfTickets, totalPriceOfTickets }: { tickets: PaginationData<TicketData>, numbersOfTickets: NumbersOfTickets, totalPriceOfTickets: number}) {
  const [numbersOfTicketsState, setNumbersOfTicketsState] = useState<NumbersOfTickets>(numbersOfTickets)
  const [totalPriceOfTicketsState, setTotalPriceOfTicketsState] = useState(totalPriceOfTickets)

  useEffect(() => {
    setTotalPriceOfTicketsState(totalPriceOfTickets)
  }, [totalPriceOfTickets])
  
  async function onClick() {
    router.get(showReview(), undefined, {
      onError: () => console.error('Failed to get to checkout')
    })
  }

  async function updateNumber(number: number, ticketId: number) {
    try {
      if (number <= 0) {
        return
      }

      const updateRoute = update(ticketId)
      const response = await axios.put(updateRoute.url, {
        number_of_tickets: number,
      })
      setNumbersOfTicketsState(prev => ({...prev, [ticketId]: response.data.numberOfTickets}))
      setTotalPriceOfTicketsState(prev => prev + response.data.differenceInTotalPrice)
    } catch (error: any) {
      if (error.response.data.sales_period !== undefined) {
        toast.error(error.response.data.sales_period)
        return
      }
      if (error.response.data.number_of_tickets !== undefined) {
        toast.error(error.response.data.number_of_tickets)
        return
      }
      toast.error('Failed to update the number of thickets')
    }
  }

  async function destroyTicket(ticketId: number) {
    router.delete(destroy(ticketId), {
      onError: () => console.error('Failed to delete a ticket')
    })
  }

  if (tickets.data.length === 0) {
    return (
      <div className="flex flex-col items-center justify-center rounded-xl border border-dashed py-20 text-center">
        <ShoppingCart className="mb-3 size-10 text-muted-foreground/50" />
        <p className="font-medium text-muted-foreground">Your cart is empty</p>
        <p className="mt-1 text-sm text-muted-foreground/70">Browse events and add tickets to get started.</p>
      </div>
    )
  }

  return (
    <div className="space-y-6">
      <h1 className="text-2xl font-bold tracking-tight">Shopping Cart</h1>

      <div className="grid gap-6 lg:grid-cols-3">
        <div className="space-y-4 lg:col-span-2">
          {tickets.data.map((ticket) => (
            <div key={ticket.id} className="space-y-3 rounded-xl border p-4">
              <Link href={showTichet(ticket.id)} className="block">
                <Ticket ticket={ticket} isEllipsis={true} className="border-0 shadow-none hover:shadow-none" />
              </Link>
              <div className="flex items-center justify-between border-t pt-3">
                <Counter number={Number(numbersOfTicketsState[ticket.id] ?? 0)} ticketId={ticket.id} updateNumber={updateNumber}/>
                <Button variant="ghost" size="sm" className="text-destructive hover:text-destructive" onClick={() => destroyTicket(ticket.id)}>
                  <Trash2 className="size-4" />
                  Remove
                </Button>
              </div>
            </div>
          ))}

          <Pagination pagination={tickets}/>
        </div>

        <div className="lg:col-span-1">
          <Card className="sticky top-24">
            <CardHeader>
              <CardTitle>Order Summary</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="flex items-center justify-between text-sm">
                <span className="text-muted-foreground">Total</span>
                <span className="text-2xl font-bold text-primary">{formatCurrency(totalPriceOfTicketsState)}</span>
              </div>
            </CardContent>
            <CardFooter>
              <Button onClick={onClick} className="w-full" size="lg">
                Proceed to Review
                <ArrowRight className="size-4" />
              </Button>
            </CardFooter>
          </Card>
        </div>
      </div>
    </div>
  )
}
