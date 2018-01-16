export const menuItems = [
  {
    title: 'Список объектов',
    routerLink: 'facilities-list',
    role: ['super-admin', 'admin'],
    icon: '',
    selected: false,
    expanded: false,
    order: 0
  },
  {
    title: 'Администраторы объектов',
    routerLink: 'admin-list',
    role: ['super-admin'],
    icon: '',
    selected: false,
    expanded: false,
    order: 100
  },
  {
    title: 'Пользователи приложения',
    routerLink: 'user-list',
    role: ['super-admin'],
    icon: '',
    selected: false,
    expanded: false,
    order: 200
  },
  {
    title: 'Преимущества',
    routerLink: 'advantages',
    role: ['super-admin'],
    icon: '',
    selected: false,
    expanded: false,
    order: 300
  },
];
