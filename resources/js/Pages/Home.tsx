import { Button } from '@/components/ui/button'
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'

export default function Home({ tickets }: { tickets: any}) {
  console.log(tickets)
  return (
    <div>
      {tickets.data.map((ticket: any) => (
        <div key={ticket.id}>
          <Card>
            <CardHeader>
              <CardTitle>{ticket.event_title}</CardTitle>
              <CardDescription className="overflow-ellipsis">
                {ticket.event_description}
              </CardDescription>
            </CardHeader>
            <CardContent>
              ${ticket.price}
            </CardContent>
            <CardFooter>
              {ticket.event_start_date}-{ticket.event_end_date}
            </CardFooter>
          </Card>
        </div>
      ))}
    </div>
  )
}
