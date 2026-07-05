import { store } from '@/actions/App/Http/Controllers/CartController'
import { useEffect, useState } from 'react'
import { router } from '@inertiajs/react'
import { toast } from 'sonner'
import { LoadingButton } from '@/components/loading-button'
import { Counter } from '@/components/counter'
import { Ticket } from '@/components/ticket'
import type { TicketData } from '@/components/ticket'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { ShoppingCart } from 'lucide-react'
import { formatCurrency } from '@/lib/utils'

export default function TicketDetail({ ticket, errors }: { ticket: TicketData, errors: Record<string, string> }) {
  const [isLoading, setIsLoading] = useState(false)
  const [numberOfTickets, setNumberOfTickets] = useState(1)

  const isOnSale = ticket.number_of_tickets !== 0;

  useEffect(() => {
    if (errors.sales_period !== undefined) {
      toast.error(errors.sales_period)
    }
    if (errors.number_of_tickets !== undefined) {
      toast.error(errors.number_of_tickets)
    }
  }, [errors]);

  async function onClick() {
    if (isLoading) {
      return
    }

    setIsLoading(true)
    router.post(store(ticket.id), { number_of_tickets: numberOfTickets }, {
      onSuccess: () => toast.success('The ticket has been added to your cart'),
      onFinish: () => setIsLoading(false)
    })
  }

  function updateNumber(number: number, _ticketId: number) {
    if (number <= 0) {
      return
    }

    setNumberOfTickets(number)
  }

  return (
    <div className="mx-auto max-w-2xl space-y-6">
      <Ticket ticket={ticket} />

      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2 text-lg">
            <ShoppingCart className="size-5 text-primary" />
            Purchase
          </CardTitle>
        </CardHeader>
        <CardContent className="space-y-5">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm text-muted-foreground">Quantity</p>
              <Counter number={numberOfTickets} ticketId={ticket.id} updateNumber={updateNumber} />
            </div>
            <div className="text-right">
              <p className="text-sm text-muted-foreground">Subtotal</p>
              <p className="text-2xl font-bold text-primary">{formatCurrency(ticket.price * numberOfTickets)}</p>
            </div>
          </div>

          <LoadingButton
            onClick={onClick}
            isLoading={isLoading}
            disabled={!isOnSale}
            className="w-full"
            size="lg"
          >
            {isOnSale ? 'Add to Cart' : 'Sold Out'}
          </LoadingButton>
        </CardContent>
      </Card>
    </div>
  )
}
