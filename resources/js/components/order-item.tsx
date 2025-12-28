import { formatCurrency } from '@/lib/utils'

import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'

export type OrderItemData = {
  event_title: string,
  event_description: string | null,
  price: number,
  number_of_tickets: number,
}

export function OrderItem({ orderItem, isEllipsis = false }: { orderItem: OrderItemData, isEllipsis?: boolean }) {
  return (
    <Card>
      <CardHeader>
        <CardTitle>{orderItem.event_title}</CardTitle>
        <CardDescription className={isEllipsis ? 'overflow-ellipsis' : ''}>
          {orderItem.event_description}
        </CardDescription>
      </CardHeader>
      <CardContent>
        {formatCurrency(orderItem.price)}
      </CardContent>
      <CardFooter>
        {orderItem.number_of_tickets}
      </CardFooter>
    </Card>
  )
}
