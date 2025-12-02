import { Order } from '@/components/order'
import type { OrderData } from '@/components/order'
import { Pagination } from '@/components/pagination'
import type { PaginationData } from '@/components/pagination'

export default function OrderHistory({ userOrders }: { userOrders: PaginationData<OrderData>}) {
  return (
    <div>
      {userOrders.data.map((userOrder) => (
        <Order key={userOrder.id} userOrder={userOrder} isDetail={false}/>
      ))}

      <Pagination pagination={userOrders}/>
    </div>
  )
}
