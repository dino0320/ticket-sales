import { showIssuedTicket } from '@/actions/App/Http/Controllers/TicketController';
import { issueTicket } from '@/routes/index';
import { Link, router } from '@inertiajs/react'
import { Button } from '@/components/ui/button'
import { Pagination } from '@/components/pagination'
import type { PaginationData } from '@/components/pagination'
import { Ticket } from '@/components/ticket'
import type { TicketData } from '@/components/ticket'

export default function IssuedTicketIndex({ tickets }: { tickets: PaginationData<TicketData>}) {
  async function onClick() {
    router.get(issueTicket(), undefined, {
      onError: () => console.error('Failed to get to issue ticket form')
    })
  }

  return (
    <div>
      <Button onClick={onClick}>Issue Ticket</Button>

      {tickets.data.map((ticket) => (
        <Link key={ticket.id} href={showIssuedTicket(ticket.id)}>
          <Ticket ticket={ticket} isEllipsis={true}/>
        </Link>
      ))}

      <Pagination pagination={tickets}/>
    </div>
  )
}
