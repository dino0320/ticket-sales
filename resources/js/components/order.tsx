import { useState } from 'react'
import { Button } from '@/components/ui/button'
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'
import { OrderItem } from '@/components/order-item'
import type { OrderItemData } from '@/components/order-item'
import { formatCurrency } from '@/lib/utils'

export type OrderData = {
  id: number,
  amount: number,
  order_items: OrderItemData[],
  order_date: string,
}

export function Order({ userOrder, isDetail = false }: { userOrder: OrderData, isDetail: boolean }) {
  const [isDetailState, setIsDetailState] = useState(isDetail);
    
  async function onClick() {
    setIsDetailState(prev => !prev)
  }

  const orderItems = isDetailState ? (
    <div>
      {userOrder.order_items.map((orderItem, index) => (
        <div key={index}>
          <OrderItem orderItem={orderItem} isEllipsis={false}/>
        </div>
      ))}
    </div>
  ) : (
    <div>
      {userOrder.order_items[0]?.event_title}{userOrder.order_items.length > 1 ? ' + others' : ''}
    </div>
  )

  return (
    <Card>
      <CardHeader>
        <CardTitle>Order ID: {userOrder.id}</CardTitle>
        <CardDescription>
          {formatCurrency(userOrder.amount)}
        </CardDescription>
      </CardHeader>
      <CardContent>
        {orderItems}
        <Button onClick={onClick}>Show {isDetailState ? 'less' : 'more'}</Button>
      </CardContent>
      <CardFooter>
        {userOrder.order_date}
      </CardFooter>
    </Card>
  )
}
