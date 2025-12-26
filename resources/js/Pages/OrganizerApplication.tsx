import { applyToBeOrganizer } from '@/actions/App/Http/Controllers/AccountController';
import { z } from 'zod'
import { zodResolver } from '@hookform/resolvers/zod'
import { useForm } from 'react-hook-form'
import { router } from '@inertiajs/react'
import { useState } from 'react';
import { setManualFormErrors } from '@/lib/form-utils'

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
import { Label } from '@/components/ui/label'
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group'
import { Textarea } from '@/components/ui/textarea'

import { LoadingButton } from '@/components/loading-button'

import { applyToBeOrganizerFormSchema } from '@/lib/validation-schemas'

const formSchema = applyToBeOrganizerFormSchema

export default function OrganizerApplication() {
  const form = useForm<z.infer<typeof formSchema>>({
    resolver: zodResolver(formSchema),
    defaultValues: {
      event_description: '',
      is_individual: true,
      website_url: '',
    },
  })

  const [errorMessage, setErrorMessage] = useState('')
  const [isLoading, setIsLoading] = useState(false)

  async function onSubmit(values: z.infer<typeof formSchema>) {
    setIsLoading(true)
    router.post(applyToBeOrganizer(), values, {
      onError: (errors: Record<string, string>) => setManualFormErrors(errors, form, setErrorMessage),
      onFinish: () => setIsLoading(false)
    })
  }

  return (
    <div className="flex min-h-[60vh] h-full w-full items-center justify-center px-4">
      <Card className="mx-auto max-w-sm">
        <CardHeader>
          <CardTitle className="text-2xl">Organizer Application</CardTitle>
          <CardDescription>
            Apply to be an organizer by filling out the form below.
            <p className="text-destructive text-sm">{errorMessage}</p>
          </CardDescription>
        </CardHeader>
        <CardContent>
          <Form {...form}>
            <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-8">
              <div className="grid gap-4">
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

                {/* Organizer Type Field */}
                <FormField
                  control={form.control}
                  name="is_individual"
                  render={({ field }) => (
                    <FormItem className="grid gap-2">
                      <FormLabel htmlFor="is_individual">Organizer Type</FormLabel>
                      <FormControl>
                        <RadioGroup
                          defaultValue={field.value ? "individual" : "company"}
                          onValueChange={(value) => field.onChange(value === "individual")}
                        >
                          <div className="flex items-center space-x-2">
                            <RadioGroupItem value="individual" id="individual" />
                            <Label htmlFor="individual">Individual</Label>
                          </div>
                          <div className="flex items-center space-x-2">
                            <RadioGroupItem value="company" id="company" />
                            <Label htmlFor="company">Company</Label>
                          </div>
                        </RadioGroup>
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />

                {/* Website URL Field */}
                <FormField
                  control={form.control}
                  name="website_url"
                  render={({ field }) => (
                    <FormItem className="grid gap-2">
                      <FormLabel htmlFor="website_url">Website URL</FormLabel>
                      <FormControl>
                        <Input
                          id="website_url"
                          placeholder="https://example.com"
                          type="url"
                          autoComplete="website_url"
                          {...field}
                        />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />

                <LoadingButton type="submit" className="w-full" isLoading={isLoading}>
                  Apply
                </LoadingButton>
              </div>
            </form>
          </Form>
        </CardContent>
      </Card>
    </div>
  )
}
