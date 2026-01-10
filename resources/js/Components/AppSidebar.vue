<template>
  <aside
    :class="[
      'fixed mt-16 flex flex-col lg:mt-0 top-0 px-5 left-0 bg-white dark:bg-gray-900 dark:border-gray-800 text-gray-900 h-screen transition-all duration-300 ease-in-out z-99999 border-r border-gray-200',
      {
        'lg:w-[290px]': isExpanded || isMobileOpen || isHovered,
        'lg:w-[90px]': !isExpanded && !isHovered,
        'translate-x-0 w-[290px]': isMobileOpen,
        '-translate-x-full': !isMobileOpen,
        'lg:translate-x-0': true,
      },
    ]"
    @mouseenter="!isExpanded && (isHovered = true)"
    @mouseleave="isHovered = false"
  >
    <div
      :class="[
        'py-8 flex',
        !isExpanded && !isHovered ? 'lg:justify-center' : 'justify-start',
      ]"
    >
      <Link href="/dashboard" class="flex items-center space-x-3">
        <div class="w-8 h-8 bg-gradient-to-br from-blue-600 to-blue-700 rounded-lg flex items-center justify-center flex-shrink-0">
          <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
          </svg>
        </div>
        <div v-if="isExpanded || isHovered || isMobileOpen" class="flex flex-col">
          <span class="text-lg font-bold text-gray-900 dark:text-white leading-tight">Archival</span>
          <span class="text-xs text-gray-500 dark:text-gray-400 -mt-1">System</span>
        </div>
      </Link>
    </div>
    <div
      class="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar"
    >
      <nav class="mb-6">
        <div class="flex flex-col gap-4">
          <div v-for="(menuGroup, groupIndex) in menuGroups" :key="groupIndex">
            <h2
              :class="[
                'mb-4 text-xs uppercase flex leading-[20px] text-gray-400',
                !isExpanded && !isHovered
                  ? 'lg:justify-center'
                  : 'justify-start',
              ]"
            >
              <template v-if="isExpanded || isHovered || isMobileOpen">
                {{ menuGroup.title }}
              </template>
              <HorizontalDots v-else />
            </h2>
            <ul class="flex flex-col gap-4">
              <li v-for="(item, index) in menuGroup.items" :key="item.name">
                <button
                  v-if="item.subItems"
                  @click="toggleSubmenu(groupIndex, index)"
                  :class="[
                    'flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors duration-200 group w-full',
                    {
                      'text-brand-500 dark:text-brand-400 bg-gray-100 dark:bg-gray-800': isSubmenuOpen(groupIndex, index),
                      'text-gray-700 dark:text-gray-300': !isSubmenuOpen(groupIndex, index),
                    },
                    !isExpanded && !isHovered
                      ? 'lg:justify-center'
                      : 'lg:justify-start',
                  ]"
                >
                  <span
                    :class="[
                      isSubmenuOpen(groupIndex, index)
                        ? 'text-brand-500'
                        : 'text-gray-400',
                    ]"
                  >
                    <component :is="item.icon" />
                  </span>
                  <span
                    v-if="isExpanded || isHovered || isMobileOpen"
                    class="ml-3 text-sm font-medium"
                    >{{ item.name }}</span
                  >
                  <ChevronDownIcon
                    v-if="isExpanded || isHovered || isMobileOpen"
                    :class="[
                      'ml-auto w-5 h-5 transition-transform duration-200',
                      {
                        'rotate-180 text-brand-500': isSubmenuOpen(
                          groupIndex,
                          index
                        ),
                      },
                    ]"
                  />
                </button>
                <a
                  v-else-if="item.external"
                  :href="item.path"
                  target="_blank"
                  rel="noopener noreferrer"
                  :class="[
                    'flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors duration-200 group',
                    {
                      'text-gray-700 dark:text-gray-300': true,
                    },
                  ]"
                >
                  <span class="text-gray-400">
                    <component :is="item.icon" />
                  </span>
                  <span
                    v-if="isExpanded || isHovered || isMobileOpen"
                    class="ml-3 text-sm font-medium"
                    >{{ item.name }}</span
                  >
                </a>
                <Link
                  v-else-if="item.path"
                  :href="item.path"
                  :class="[
                    'flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors duration-200 group',
                    {
                      'text-brand-500 dark:text-brand-400 bg-gray-100 dark:bg-gray-800': isActive(item.path),
                      'text-gray-700 dark:text-gray-300': !isActive(item.path),
                    },
                  ]"
                >
                  <span
                    :class="[
                      isActive(item.path)
                        ? 'text-brand-500'
                        : 'text-gray-400',
                    ]"
                  >
                    <component :is="item.icon" />
                  </span>
                  <span
                    v-if="isExpanded || isHovered || isMobileOpen"
                    class="ml-3 text-sm font-medium"
                    >{{ item.name }}</span
                  >
                </Link>
                <transition
                  @enter="startTransition"
                  @after-enter="endTransition"
                  @before-leave="startTransition"
                  @after-leave="endTransition"
                >
                  <div
                    v-show="
                      isSubmenuOpen(groupIndex, index) &&
                      (isExpanded || isHovered || isMobileOpen)
                    "
                  >
                    <ul class="mt-2 space-y-1 ml-9">
                      <template v-for="subItem in item.subItems" :key="subItem.name">
                        <li v-if="!shouldHideNavPath(subItem.path)">
                        <button
                          v-if="subItem.subItems"
                          @click="toggleSubmenu(groupIndex, `${index}-${subItem.name}`)"
                          :class="[
                            'flex items-center px-3 py-2 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200 w-full text-left',
                            {
                              'text-brand-500 dark:text-brand-400 bg-gray-100 dark:bg-gray-800': isSubmenuOpen(groupIndex, `${index}-${subItem.name}`),
                              'text-gray-700 dark:text-gray-300': !isSubmenuOpen(groupIndex, `${index}-${subItem.name}`),
                            },
                          ]"
                        >
                          <component :is="subItem.icon || DocsIcon" class="w-4 h-4 mr-2" />
                          {{ subItem.name }}
                          <ChevronDownIcon
                            :class="[
                              'ml-auto w-4 h-4 transition-transform duration-200',
                              {
                                'rotate-180 text-brand-500': isSubmenuOpen(groupIndex, `${index}-${subItem.name}`),
                              },
                            ]"
                          />
                        </button>
                        <Link
                          v-else
                          :href="subItem.path"
                          :class="[
                            'flex items-center px-3 py-2 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200',
                            {
                              'text-brand-500 dark:text-brand-400 bg-gray-100 dark:bg-gray-800': isActive(
                                subItem.path
                              ),
                              'text-gray-700 dark:text-gray-300': !isActive(
                                subItem.path
                              ),
                            },
                          ]"
                        >
                          <component :is="subItem.icon || DocsIcon" class="w-4 h-4 mr-2" />
                          {{ subItem.name }}
                          <span class="flex items-center gap-1 ml-auto">
                            <span
                              v-if="subItem.new"
                              :class="[
                                'px-2 py-0.5 text-xs font-medium rounded-full',
                                {
                                  'bg-brand-500 text-white': isActive(
                                    subItem.path
                                  ),
                                  'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300': !isActive(
                                    subItem.path
                                  ),
                                },
                              ]"
                            >
                              new
                            </span>
                            <span
                              v-if="subItem.pro"
                              :class="[
                                'px-2 py-0.5 text-xs font-medium rounded-full',
                                {
                                  'bg-brand-500 text-white': isActive(
                                    subItem.path
                                  ),
                                  'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300': !isActive(
                                    subItem.path
                                  ),
                                },
                              ]"
                            >
                              pro
                            </span>
                          </span>
                        </Link>
                        <!-- Nested sub-subitems -->
                        <transition
                          v-if="subItem.subItems"
                          @enter="startTransition"
                          @after-enter="endTransition"
                          @before-leave="startTransition"
                          @after-leave="endTransition"
                        >
                          <div
                            v-show="isSubmenuOpen(groupIndex, `${index}-${subItem.name}`)"
                          >
                            <ul class="mt-1 space-y-1 ml-6">
                              <li v-for="subSubItem in subItem.subItems" :key="subSubItem.name">
                                <Link
                                  :href="subSubItem.path"
                                  :class="[
                                    'flex items-center px-3 py-1.5 text-xs rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200',
                                    {
                                      'text-brand-500 dark:text-brand-400 bg-gray-100 dark:bg-gray-800': isActive(
                                        subSubItem.path
                                      ),
                                      'text-gray-700 dark:text-gray-300': !isActive(
                                        subSubItem.path
                                      ),
                                    },
                                  ]"
                                >
                                  <component :is="subSubItem.icon || DocsIcon" class="w-3 h-3 mr-2" />
                                  {{ subSubItem.name }}
                                </Link>
                              </li>
                            </ul>
                          </div>
                        </transition>
                        </li>
                      </template>
                    </ul>
                  </div>
                </transition>
              </li>
            </ul>
          </div>
        </div>
      </nav>
    </div>
  </aside>
</template>

<script setup>
import { ref, computed } from "vue"
import { usePage } from "@inertiajs/vue3"
import { Link } from "@inertiajs/vue3"
import {
  GridIcon,
  CalenderIcon,
  UserCircleIcon,
  ChatIcon,
  MailIcon,
  DocsIcon,
  PieChartIcon,
  ChevronDownIcon,
  HorizontalDots,
  PageIcon,
  TableIcon,
  ListIcon,
  PlugInIcon,
  SettingsIcon,
  LogoutIcon,
  PlusIcon,
  UserGroupIcon,
  ArchiveIcon,
} from "@/icons"
import BoxCubeIcon from "@/icons/BoxCubeIcon.vue"

const page = usePage()
const spTerms = computed(() => page.props.spTerms || [])

const currentUser = computed(() => page.props.auth?.user ?? {})
const isStaff = computed(() => (currentUser.value?.role ?? '').toString().trim().toLowerCase() === 'staff')

const shouldHideNavPath = (path) => {
  if (!path) return false
  if (isStaff.value && path === '/system-logs') return true
  return false
}

const isExpanded = ref(false)
const isMobileOpen = ref(false)
const isHovered = ref(false)
const openSubmenu = ref(new Set())

// Helper function to get year range for display
const getYearRange = (term) => {
  if (!term.start_year || !term.end_year) return ''
  const startYear = new Date(term.start_year).getFullYear()
  const endYear = new Date(term.end_year).getFullYear()
  return `${startYear}-${endYear}`
}

// Generate SP menu items dynamically from database
const generateSpMenuItems = () => {
  return spTerms.value.map(term => {
    const termSlug = `sp-${term.term_no.toLowerCase()}`
    return {
      name: `${term.term_no.toUpperCase()} SP (${getYearRange(term)})`,
      subItems: [
        {
          name: "Officials",
          path: `/${termSlug}/officials`,
          icon: UserGroupIcon,
        },
        {
          name: "Add officials",
          path: `/${termSlug}/add-officials`,
          icon: PlusIcon,
        },
      ],
    }
  })
}

const menuGroups = [
  {
    title: "Archival System",
    items: [
      {
        icon: GridIcon,
        name: "Dashboard",
        path: "/dashboard",
      },
      {
        icon: DocsIcon,
        name: "Documents",
        subItems: [
          {
            name: "Ordinances",
            icon: DocsIcon,
            subItems: [
              {
                name: "Overall",
                path: "/ordinances",
                icon: DocsIcon,
              },
              {
                name: "Provinces",
                path: "/ordinances/provinces",
                icon: DocsIcon,
              },
              {
                name: "Municipalities",
                path: "/municipal-ordinances",
                icon: DocsIcon,
              },
            ],
          },
          {
            name: "Resolutions",
            icon: DocsIcon,
            subItems: [
              {
                name: "Overall",
                path: "/resolutions",
                icon: DocsIcon,
              },
              {
                name: "Provinces",
                path: "/resolutions/provinces",
                icon: DocsIcon,
              },
              {
                name: "Municipalities",
                path: "/municipal-resolutions",
                icon: DocsIcon,
              },
            ],
          },
          {
            name: "Draft Ordinances",
            path: "/draft-ordinances",
            icon: PageIcon,
          },
          {
            name: "Draft Resolutions",
            path: "/draft-resolutions",
            icon: PageIcon,
          },
        ],
      },
      {
        icon: ListIcon,
        name: "Offices",
        subItems: [
          {
            name: "Municipal Communications",
            path: "/municipal-communications",
            icon: ChatIcon,
          },
          {
            name: "National Office Communications",
            path: "/national-office-communications",
            icon: TableIcon,
          },
          {
            name: "Provincial Offices",
            path: "/provincial-offices",
            icon: TableIcon,
          },
        ],
      },
      {
        icon: ListIcon,
        name: "Programs",
        subItems: [
          {
            name: "Accomplishments",
            path: "/accomplishments",
            icon: PieChartIcon,
          },
          {
            name: "Projects",
            path: "/projects",
            icon: PlugInIcon,
          },
          {
            name: "MOAs",
            path: "/memorandum-of-agreements",
            icon: MailIcon,
          },
          {
            name: "Committee Reports",
            path: "/committee-reports",
            icon: DocsIcon,
          },
        ],
      },
            {
        icon: SettingsIcon,
        name: "Settings",
        subItems: [
          {
            name: "Tools",
            path: "/settings",
            icon: SettingsIcon,
          },
          {
            name: "Manage Accounts",
            path: "/settings/manage-accounts",
            icon: UserGroupIcon,
          },
          {
            name: "Add Accounts",
            path: "/settings/add-accounts",
            icon: PlusIcon,
          },
          {
            name: "Change Password",
            path: "/settings/change-password",
            icon: SettingsIcon,
          },
          {
            name: "SP Terms",
            path: "/sp-terms",
            icon: CalenderIcon,
          },
          {
            name: "System Logs",
            path: "/system-logs",
            icon: ArchiveIcon, // Changed from DocsIcon
          },
          {
            name: "Laravel Logs",
            path: "/log-viewer",
            icon: ArchiveIcon,
            external: true,
          },
          {
            name: "Log Out",
            path: "/logout",
            icon: LogoutIcon,
          },
        ],
      },
      {
        icon: UserGroupIcon,
        name: "SANGGUNIAN PANLALAWIGAN",
        subItems: generateSpMenuItems(),
      }



    ],
  },
]

const isActive = (path) => page.url === path

const toggleSubmenu = (groupIndex, itemIndex) => {
  const key = `${groupIndex}-${itemIndex}`
  if (openSubmenu.value.has(key)) {
    openSubmenu.value.delete(key)
  } else {
    openSubmenu.value.add(key)
  }
}

const isAnySubmenuRouteActive = computed(() => {
  return menuGroups.some((group) =>
    group.items.some(
      (item) =>
        item.subItems && item.subItems.some((subItem) =>
          isActive(subItem.path) || (subItem.subItems && subItem.subItems.some((subSubItem) => isActive(subSubItem.path)))
        )
    )
  )
})

const isSubmenuOpen = (groupIndex, itemIndex) => {
  const key = `${groupIndex}-${itemIndex}`
  if (openSubmenu.value.has(key)) {
    return true
  }

  // Handle nested submenu keys like "4-1ST SP (2001-2004)"
  if (typeof itemIndex === 'string' && itemIndex.includes('-')) {
    const parts = itemIndex.split('-')
    const parentIndex = parseInt(parts[0])
    const subItemName = parts.slice(1).join('-')

    const parentItem = menuGroups[groupIndex]?.items[parentIndex]
    if (parentItem?.subItems) {
      const subItem = parentItem.subItems.find(item => item.name === subItemName)
      return subItem?.subItems?.some((subSubItem) => isActive(subSubItem.path)) || false
    }
    return false
  }

  // Handle regular menu item indices
  const item = menuGroups[groupIndex]?.items[itemIndex]
  return (
    isAnySubmenuRouteActive.value &&
    item?.subItems?.some((subItem) =>
      isActive(subItem.path) || subItem.subItems?.some((subSubItem) => isActive(subSubItem.path))
    )
  )
}

const startTransition = (el) => {
  el.style.height = "auto"
  const height = el.scrollHeight
  el.style.height = "0px"
  el.offsetHeight // force reflow
  el.style.height = height + "px"
}

const endTransition = (el) => {
  el.style.height = ""
}
</script>