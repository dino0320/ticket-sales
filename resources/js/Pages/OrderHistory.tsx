import { Order } from '@/components/order'
import type { OrderData } from '@/components/order'
import { Pagination } from '@/components/pagination'
import type { PaginationData } from '@/components/pagination'

export default function OrderHistory({ userOrders }: { userOrders: PaginationData<OrderData>}) {
  return (
    <div className="space-y-1">
      <h2 className="text-2xl font-bold">Order History</h2>

      {userOrders.data.map((userOrder) => (
        <Order key={userOrder.id} userOrder={userOrder} isDetail={false}/>
      ))}

      <Pagination pagination={userOrders}/>
    </div>
  )
}
