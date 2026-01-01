import {
  Pagination as SPagination,
  PaginationContent,
  PaginationEllipsis,
  PaginationItem,
  PaginationLink,
  PaginationNext,
  PaginationPrevious,
} from '@/components/ui/pagination'

type Link = {
  url: string | null,
  label: string,
  active: boolean,
}

export type PaginationData<T> = {
  data: T[],
  meta: {
    links: Link[],
  },
}

export function Pagination<T>({ pagination }: { pagination: PaginationData<T> }) {
  const links = pagination.meta.links.map((link: Link, index: number) => {
    if (index === 0) {
      return link.url === null ? null : (
        <PaginationItem key={index}>
          <PaginationPrevious href={link.url} />
        </PaginationItem>
      )
    }

    if (index === pagination.meta.links.length - 1) {
      return link.url === null ? null : (
        <PaginationItem key={index}>
          <PaginationNext href={link.url} />
        </PaginationItem>
      )
    }

    if (link.url === null) {
      return (
        <PaginationItem key={index}>
          <PaginationEllipsis />
        </PaginationItem>
      )
    }

    return (
      <PaginationItem key={index}>
        <PaginationLink href={link.url} isActive={link.active}>{link.label}</PaginationLink>
      </PaginationItem>
    )
  })

  return (
    <SPagination>
      <PaginationContent>
        {links}
      </PaginationContent>
    </SPagination>
  )
}
