import {
  Pagination as SPagination,
  PaginationContent,
  PaginationItem,
  PaginationNext,
  PaginationPrevious,
} from '@/components/ui/pagination'

export type PaginationData<T> = {
  data: T[],
  prev_page_url: string | null,
  next_page_url: string | null,
}

export function Pagination<T>({ pagination }: { pagination: PaginationData<T> }) {
  const prevPageLink = pagination.prev_page_url === null ? null : (
    <PaginationItem>
      <PaginationPrevious href={pagination.prev_page_url} />
    </PaginationItem>
  )

  const nextPageLink = pagination.next_page_url === null ? null : (
    <PaginationItem>
      <PaginationNext href={pagination.next_page_url} />
    </PaginationItem>
  )

  return (
    <SPagination>
      <PaginationContent>
        {prevPageLink}
        {nextPageLink}
      </PaginationContent>
    </SPagination>
  )
}
