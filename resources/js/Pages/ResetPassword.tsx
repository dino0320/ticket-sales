import { resetPassword } from '@/actions/App/Http/Controllers/AccountController';
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
import { Button } from '@/components/ui/button'
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { PasswordInput } from '@/components/ui/password-input'

import { resetPasswordFormSchema } from '@/lib/validation-schemas'

const formSchema = resetPasswordFormSchema

export default function ResetPassword() {
  const form = useForm<z.infer<typeof formSchema>>({
    resolver: zodResolver(formSchema),
    defaultValues: {
      email: '',
      password: '',
      new_password: '',
      new_password_confirmation: '',
    },
  })

  const [errorMessage, setErrorMessage] = useState('');

  async function onSubmit(values: z.infer<typeof formSchema>) {
    try {
      // Assuming an async registration function
      router.post(resetPassword(), values, { onError: (errors: Record<string, string>) => setManualFormErrors(errors, form, setErrorMessage) })
    } catch (error) {
      console.error('Form submission error', error)
    }
  }

  return (
    <div className="flex min-h-[60vh] h-full w-full items-center justify-center px-4">
      <Card className="mx-auto max-w-sm">
        <CardHeader>
          <CardTitle className="text-2xl">Reset your password</CardTitle>
          <CardDescription>
            Reset your password by filling out the form below.
            <p className="text-destructive text-sm">{errorMessage}</p>
          </CardDescription>
        </CardHeader>
        <CardContent>
          <Form {...form}>
            <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-8">
              <div className="grid gap-4">
                {/* Email Field */}
                <FormField
                  control={form.control}
                  name="email"
                  render={({ field }) => (
                    <FormItem className="grid gap-2">
                      <FormLabel htmlFor="email">Email</FormLabel>
                      <FormControl>
                        <Input
                          id="email"
                          placeholder="johndoe@mail.com"
                          type="email"
                          autoComplete="email"
                          {...field}
                        />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />

                {/* Password Field */}
                <FormField
                  control={form.control}
                  name="password"
                  render={({ field }) => (
                    <FormItem className="grid gap-2">
                      <FormLabel htmlFor="password">Password</FormLabel>
                      <FormControl>
                        <PasswordInput
                          id="password"
                          placeholder="******"
                          autoComplete="your-password"
                          {...field}
                        />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />

                {/* New Password Field */}
                <FormField
                  control={form.control}
                  name="new_password"
                  render={({ field }) => (
                    <FormItem className="grid gap-2">
                      <FormLabel htmlFor="new_password">New Password</FormLabel>
                      <FormControl>
                        <PasswordInput
                          id="new_password"
                          placeholder="******"
                          autoComplete="new-password"
                          {...field}
                        />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />

                {/* Confirm New Password Field */}
                <FormField
                  control={form.control}
                  name="new_password_confirmation"
                  render={({ field }) => (
                    <FormItem className="grid gap-2">
                      <FormLabel htmlFor="new_password_confirmation">
                        Confirm New Password
                      </FormLabel>
                      <FormControl>
                        <PasswordInput
                          id="new_password_confirmation"
                          placeholder="******"
                          autoComplete="new-password"
                          {...field}
                        />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />

                <Button type="submit" className="w-full">
                  Submit
                </Button>
              </div>
            </form>
          </Form>
        </CardContent>
      </Card>
    </div>
  )
}
