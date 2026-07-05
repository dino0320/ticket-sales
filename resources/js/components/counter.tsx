import { Minus, Plus } from 'lucide-react'
import { Button } from '@/components/ui/button'
import { cn } from '@/lib/utils'

export function Counter({ number, ticketId, updateNumber, className }: { number: number, ticketId: number, updateNumber: (number: number, ticketId: number) => void, className?: string }) {
  function onClick(isPlus: boolean = true) {
    updateNumber(isPlus ? number + 1 : number - 1, ticketId)
  }

  return (
    <div className={cn('inline-flex items-center gap-1 rounded-lg border bg-background p-1', className)}>
      <Button
        type="button"
        variant="ghost"
        size="icon-sm"
        onClick={() => onClick(false)}
        disabled={number <= 1}
        aria-label="Decrease quantity"
      >
        <Minus className="size-4" />
      </Button>
      <span className="w-8 text-center text-sm font-semibold tabular-nums">{number}</span>
      <Button
        type="button"
        variant="ghost"
        size="icon-sm"
        onClick={() => onClick()}
        aria-label="Increase quantity"
      >
        <Plus className="size-4" />
      </Button>
    </div>
  )
}
