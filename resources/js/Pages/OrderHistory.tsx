import { Order } from '@/components/order'
import type { OrderData } from '@/components/order'
import { Pagination } from '@/components/pagination'
import type { PaginationData } from '@/components/pagination'
import { History } from 'lucide-react'

export default function OrderHistory({ userOrders }: { userOrders: PaginationData<OrderData>}) {
  return (
    <div className="space-y-6">
      <h1 className="text-2xl font-bold tracking-tight">Order History</h1>

      {userOrders.data.length === 0 ? (
        <div className="flex flex-col items-center justify-center rounded-xl border border-dashed py-16 text-center">
          <History className="mb-3 size-10 text-muted-foreground/50" />
          <p className="font-medium text-muted-foreground">No orders yet</p>
        </div>
      ) : (
        <div className="space-y-4">
          {userOrders.data.map((userOrder) => (
            <Order key={userOrder.id} userOrder={userOrder} isDetail={false}/>
          ))}
        </div>
      )}

      <Pagination pagination={userOrders}/>
    </div>
  )
}
