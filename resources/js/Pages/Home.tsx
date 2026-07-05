import { show } from '@/actions/App/Http/Controllers/TicketController'
import { Link } from '@inertiajs/react'
import { Pagination } from '@/components/pagination'
import type { PaginationData } from '@/components/pagination'
import { Ticket } from '@/components/ticket'
import type { TicketData } from '@/components/ticket'
import { Ticket as TicketIcon } from 'lucide-react'

export default function Home({ tickets }: { tickets: PaginationData<TicketData>}) {
  return (
    <div className="space-y-8">
      <section className="relative overflow-hidden rounded-2xl bg-primary px-6 py-10 text-primary-foreground sm:px-10 sm:py-14">
        <div className="relative z-10 max-w-xl space-y-3">
          <h1 className="text-3xl font-bold tracking-tight sm:text-4xl">
            Upcoming Events
          </h1>
          <p className="text-primary-foreground/80 text-base sm:text-lg">
            Browse available tickets and purchase in just a few clicks.
          </p>
        </div>
        <TicketIcon className="absolute -right-4 -bottom-4 size-40 opacity-10 sm:size-52" />
      </section>

      <section className="space-y-5">
        <div className="flex items-center justify-between">
          <h2 className="text-2xl font-bold tracking-tight">Upcoming Tickets</h2>
          <span className="text-sm text-muted-foreground">{tickets.data.length} events</span>
        </div>

        {tickets.data.length === 0 ? (
          <div className="flex flex-col items-center justify-center rounded-xl border border-dashed py-16 text-center">
            <TicketIcon className="mb-3 size-10 text-muted-foreground/50" />
            <p className="font-medium text-muted-foreground">No tickets available right now.</p>
            <p className="mt-1 text-sm text-muted-foreground/70">Check back soon for new events.</p>
          </div>
        ) : (
          <div className="grid gap-4 sm:grid-cols-2">
            {tickets.data.map((ticket) => (
              <Link key={ticket.id} href={show(ticket.id)} className="block">
                <Ticket ticket={ticket} isEllipsis={true} className="h-full" />
              </Link>
            ))}
          </div>
        )}

        <Pagination pagination={tickets}/>
      </section>
    </div>
  )
}
