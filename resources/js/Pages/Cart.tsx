import { show as showTichet } from '@/actions/App/Http/Controllers/TicketController'
import { update, destroy } from '@/actions/App/Http/Controllers/CartController'
import { show as showReview } from '@/actions/App/Http/Controllers/CheckoutController'
import { useState, useEffect } from 'react'
import { FaRegTrashAlt } from 'react-icons/fa'
import { Link, router } from '@inertiajs/react'
import { Button } from '@/components/ui/button'
import { toast } from 'sonner'
import { Counter } from '@/components/counter'
import { Pagination } from '@/components/pagination'
import type { PaginationData } from '@/components/pagination'
import { Ticket } from '@/components/ticket'
import type { TicketData } from '@/components/ticket'
import { formatCurrency } from '@/lib/utils'
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

  return (
    <div className="space-y-1">
      {tickets.data.length === 0 ? (
        <p>No items in your cart.</p>
      ) : (
        <div>
          <Button onClick={onClick}>Proceed to review</Button>
          <p>{formatCurrency(totalPriceOfTicketsState)}</p>
        </div>
      )}

      {tickets.data.map((ticket) => (
        <div key={ticket.id}>
          <Link href={showTichet(ticket.id)}>
            <Ticket ticket={ticket} isEllipsis={true}/>
          </Link>
          <div className="flex items-center gap-3">
            <Counter number={Number(numbersOfTicketsState[ticket.id] ?? 0)} ticketId={ticket.id} updateNumber={updateNumber}/>
            <FaRegTrashAlt onClick={() => destroyTicket(ticket.id)}/>
          </div>
        </div>
      ))}

      <Pagination pagination={tickets}/>
    </div>
  )
}
