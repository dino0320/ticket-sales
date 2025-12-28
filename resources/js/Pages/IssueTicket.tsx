import { store } from '@/actions/App/Http/Controllers/TicketController';
import { z } from 'zod'
import { zodResolver } from '@hookform/resolvers/zod'
import { useForm } from 'react-hook-form'
import { router } from '@inertiajs/react'
import { useState } from 'react';
import { setManualFormErrors, convertZodError } from '@/lib/form-utils'

import {
  Form,
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from '@/components/ui/form'
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'

import { issueTicketFormSchema, issueTicketValidationSchema } from '@/lib/validation-schemas'

import { DatetimePicker } from '@/components/datetime-picker'
import { LoadingButton } from '@/components/loading-button'

const formSchema = issueTicketFormSchema

export default function IssueTicket() {
  const form = useForm<z.infer<typeof formSchema>>({
    resolver: zodResolver(formSchema),
    defaultValues: {
      event_title: '',
      event_description: '',
      price: 1,
      number_of_tickets: 1,
      event_start_date: undefined,
      event_end_date: undefined,
      start_date: undefined,
      end_date: undefined,
    },
  })

  const [errorMessage, setErrorMessage] = useState('')
  const [isLoading, setIsLoading] = useState(false)

  async function onSubmit(values: z.infer<typeof formSchema>) {
    const result = issueTicketValidationSchema.safeParse(values)
    if (!result.success) {
      setManualFormErrors(convertZodError(result.error), form, setErrorMessage)
      return
    }
      
    setIsLoading(true)
    router.post(store(), values, {
      onError: (errors: Record<string, string>) => setManualFormErrors(errors, form, setErrorMessage),
      onFinish: () => setIsLoading(false)
    })
  }

  return (
    <div className="flex min-h-[60vh] h-full w-full items-center justify-center px-4">
      <Card className="mx-auto max-w-sm">
        <CardHeader>
          <CardTitle className="text-2xl">Issue Ticket</CardTitle>
          <CardDescription>
            Issue a ticket by filling out the form below.
            <p className="text-destructive text-sm">{errorMessage}</p>
          </CardDescription>
        </CardHeader>
        <CardContent>
          <Form {...form}>
            <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-8">
              <div className="grid gap-4">
                {/* Event Title Field */}
                <FormField
                  control={form.control}
                  name="event_title"
                  render={({ field }) => (
                    <FormItem className="grid gap-2">
                      <FormLabel htmlFor="event_title">Event Title</FormLabel>
                      <FormControl>
                        <Input id="event_title" placeholder="" {...field} />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />

                {/* Event Description Field */}
                <FormField
                  control={form.control}
                  name="event_description"
                  render={({ field }) => (
                    <FormItem className="grid gap-2">
                      <FormLabel htmlFor="event_description">Event Description</FormLabel>
                      <FormControl>
                        <Textarea
                          id="event_description"
                          {...field}
                        />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />

                {/* Price Field */}
                <FormField
                  control={form.control}
                  name="price"
                  render={({ field }) => (
                    <FormItem className="grid gap-2">
                      <FormLabel htmlFor="price">Price (USD)</FormLabel>
                      <FormControl>
                        <Input id="price" type="number" placeholder="" min="1" {...field} />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />

                {/* The Number of Tickets Field */}
                <FormField
                  control={form.control}
                  name="number_of_tickets"
                  render={({ field }) => (
                    <FormItem className="grid gap-2">
                      <FormLabel htmlFor="number_of_tickets">The Number of Tickets</FormLabel>
                      <FormControl>
                        <Input id="number_of_tickets" type="number" placeholder="" min="1" {...field} />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />

                {/* Event Start Date Field */}
                <FormField
                  control={form.control}
                  name="event_start_date"
                  render={({ field }) => (
                    <FormItem className="grid gap-2">
                      <FormLabel htmlFor="event_start_date">Event Start Date</FormLabel>
                      <FormControl>
                        <DatetimePicker field={field} />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />

                {/* Event End Date Field */}
                <FormField
                  control={form.control}
                  name="event_end_date"
                  render={({ field }) => (
                    <FormItem className="grid gap-2">
                      <FormLabel htmlFor="event_end_date">Event End Date</FormLabel>
                      <FormControl>
                        <DatetimePicker field={field} />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />

                {/* Start Date Field */}
                <FormField
                  control={form.control}
                  name="start_date"
                  render={({ field }) => (
                    <FormItem className="grid gap-2">
                      <FormLabel htmlFor="start_date">Start Date</FormLabel>
                      <FormControl>
                        <DatetimePicker field={field} />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />

                {/* End Date Field */}
                <FormField
                  control={form.control}
                  name="end_date"
                  render={({ field }) => (
                    <FormItem className="grid gap-2">
                      <FormLabel htmlFor="end_date">End Date</FormLabel>
                      <FormControl>
                        <DatetimePicker field={field} />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />

                <LoadingButton type="submit" className="w-full" isLoading={isLoading}>
                  Submit
                </LoadingButton>
              </div>
            </form>
          </Form>
        </CardContent>
      </Card>
    </div>
  )
}
