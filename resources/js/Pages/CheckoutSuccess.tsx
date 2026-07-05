import { index } from '@/actions/App/Http/Controllers/HomeController'
import { Link } from '@inertiajs/react'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import { CheckCircle2 } from 'lucide-react'

export default function CheckoutSuccess({ userOrderId }: { userOrderId: number}) {
  return (
    <div className="mx-auto flex max-w-md flex-col items-center py-12 text-center">
      <Card className="w-full">
        <CardContent className="flex flex-col items-center gap-5 pt-8 pb-8">
          <div className="flex size-16 items-center justify-center rounded-full bg-primary/10">
            <CheckCircle2 className="size-8 text-primary" />
          </div>

          <div className="space-y-2">
            <h1 className="text-2xl font-bold tracking-tight">Thank you for your purchase!</h1>
            <p className="text-muted-foreground">Your order has been confirmed.</p>
          </div>

          <div className="w-full rounded-lg bg-muted px-4 py-3">
            <p className="text-sm text-muted-foreground">Order ID</p>
            <p className="text-lg font-semibold tabular-nums">#{userOrderId}</p>
          </div>

          <Button asChild className="w-full" size="lg">
            <Link href={index()}>Back to Home</Link>
          </Button>
        </CardContent>
      </Card>
    </div>
  )
}
