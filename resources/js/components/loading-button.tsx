import { Button, buttonVariants } from '@/components/ui/button'
import { Spinner } from '@/components/ui/spinner'
import type { VariantProps } from 'class-variance-authority'

export function LoadingButton({
  isLoading = false,
  ...props
}: React.ComponentProps<"button"> &
  VariantProps<typeof buttonVariants> & {
    isLoading?: boolean,
    asChild?: boolean
  }) {
  return (
    <Button {...props} disabled={props.disabled || isLoading}>
      {isLoading ? <Spinner /> : ''}
      {props.children}
    </Button>
  )
}
