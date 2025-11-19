import { show as showTichet } from '@/actions/App/Http/Controllers/TicketController';
import { update, destroy } from '@/actions/App/Http/Controllers/CartController';
import { show as showCheckout } from '@/actions/App/Http/Controllers/CheckoutController';
import { useState, useEffect } from 'react'
import { FaRegTrashAlt } from 'react-icons/fa';
import { Link } from '@inertiajs/react'
import { router } from '@inertiajs/react'
import { Button } from '@/components/ui/button'
import { Counter } from '@/components/counter'
import { Pagination } from '@/components/pagination'
import type { PaginationData } from '@/components/pagination'
import { Ticket } from '@/components/ticket'
import type { TicketData } from '@/components/ticket'
import axios from 'axios';

type NumberOfTickets = {[id: number]: number}

export default function Cart({ tickets, numberOfTickets, totalPriceOfTickets }: { tickets: PaginationData<TicketData>, numberOfTickets: NumberOfTickets, totalPriceOfTickets: number}) {
  const [numberOfTicketsState, setNumberOfTicketsState] = useState<NumberOfTickets>(numberOfTickets)
  const [totalPriceOfTicketsState, setTotalPriceOfTicketsState] = useState(totalPriceOfTickets)

  useEffect(() => {
    setTotalPriceOfTicketsState(totalPriceOfTickets)
  }, [totalPriceOfTickets])
  
  async function onClick() {
    try {
      router.get(showCheckout())
    } catch (error) {
      console.error('Can\'t go to checkout', error)
    }
  }

  async function updateNumber(number: number, ticketId: number) {
    try {
      if (number < 1) {
        return
      }

      const updateRoute = update(ticketId)
      const response = await axios.post(updateRoute.url, {
        number_of_tickets: number,
      });
      setNumberOfTicketsState(prev => ({...prev, [ticketId]: response.data.numberOfTickets}))
      setTotalPriceOfTicketsState(prev => prev - response.data.differenceInTotalPrice)
    } catch (error) {
      console.error('Can\'t update the number of thickets', error)
    }
  }

  async function destroyTicket(ticketId: number) {
    try {
      router.delete(destroy(ticketId))
    } catch (error) {
      console.error('Can\'t delete a thicket', error)
    }
  }

  return (
    <div>
      <Button onClick={onClick}>Proceed to review</Button>
      <p>${totalPriceOfTicketsState}</p>

      {tickets.data.map((ticket) => (
        <div key={ticket.id}>
          <Link href={showTichet(ticket.id)}>
            <Ticket ticket={ticket} isEllipsis={true}/>
          </Link>
          <Counter number={numberOfTicketsState[ticket.id] ?? 0} ticketId={ticket.id} updateNumber={updateNumber}/>
          <FaRegTrashAlt onClick={() => destroyTicket(ticket.id)}/>
        </div>
      ))}

      <Pagination pagination={tickets}/>
    </div>
  )
}
