export default function CheckoutSuccess({ userOrderId }: { userOrderId: number}) {
  return (
    <div>
      <p>Thank you for your purchase!</p>

      <p>Order ID: {userOrderId}</p>
    </div>
  )
}
