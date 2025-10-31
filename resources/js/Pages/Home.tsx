import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'
import { Pagination } from "@/components/pagination"
import type { PaginationData } from '@/components/pagination'

type Ticket = {
  event_title: string,
  event_description: string | null,
  price: number,
  event_start_date: string,
  event_end_date: string | null,
}

export default function Home({ tickets }: { tickets: PaginationData<Ticket>}) {
  return (
    <div>
      {tickets.data.map((ticket, index) => (
        <Card key={index}>
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
      ))}

      <Pagination pagination={tickets}/>
    </div>
  )
}
