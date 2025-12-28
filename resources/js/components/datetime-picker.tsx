import type { ControllerRenderProps, FieldValues } from 'react-hook-form'
import * as React from 'react'
import { ChevronDownIcon } from 'lucide-react'

import { Button } from '@/components/ui/button'
import { Calendar } from '@/components/ui/calendar'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover'

export function DatetimePicker<TFieldValues extends FieldValues>({ field }: {field: ControllerRenderProps<TFieldValues> }) {
  const [open, setOpen] = React.useState(false)
  const datetime: Date | undefined = field.value

  return (
    <div className="flex gap-4">
      <div className="flex flex-col gap-3">
        <Label htmlFor="date-picker" className="px-1">
          Date
        </Label>
        <Popover open={open} onOpenChange={setOpen}>
          <PopoverTrigger asChild>
            <Button
              variant="outline"
              id="date-picker"
              className="w-32 justify-between font-normal"
            >
              {datetime === undefined ? "Select date" : datetime.toLocaleDateString()}
              <ChevronDownIcon />
            </Button>
          </PopoverTrigger>
          <PopoverContent className="w-auto overflow-hidden p-0" align="start">
            <Calendar
              mode="single"
              selected={datetime}
              captionLayout="dropdown"
              onSelect={(nextDate) => {
                setOpen(false)
                if (nextDate === undefined) {
                  return
                }

                if (datetime === undefined) {
                  field.onChange(nextDate)
                  return
                }

                const nextDatetime = new Date(datetime)
                nextDatetime.setFullYear(nextDate.getFullYear(), nextDate.getMonth(), nextDate.getDate())
                field.onChange(nextDatetime)
              }}
              defaultMonth={datetime === undefined ? new Date() : datetime}
            />
          </PopoverContent>
        </Popover>
      </div>
      <div className="flex flex-col gap-3">
        <Label htmlFor="time-picker" className="px-1">
          Time
        </Label>
        <Input
          type="time"
          id="time-picker"
          step="1"
          value={datetime === undefined ? '' : `${String(datetime.getHours()).padStart(2, '0')}:${String(datetime.getMinutes()).padStart(2, '0')}:${String(datetime.getSeconds()).padStart(2, '0')}`}
          className="bg-background appearance-none [&::-webkit-calendar-picker-indicator]:hidden [&::-webkit-calendar-picker-indicator]:appearance-none"
          onChange={(event) => {
            if (datetime === undefined) {
              return
            }

            const newDatetime = new Date(datetime)
            const time = event.target.value
            const [hours, minutes, seconds] = time.split(':')
            newDatetime.setHours(hours === undefined ? 0 : Number(hours), minutes === undefined ? 0 : Number(minutes), seconds === undefined ? 0 : Number(seconds))
            field.onChange(newDatetime)
          }}
        />
      </div>
    </div>
  )
}
