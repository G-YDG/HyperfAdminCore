# 介绍

# [HyperfAdminCore](https://github.com/G-YDG/HyperfAdminCore)

📦 Hyperf Admin Core Code

## 目录说明

```text
./src                                       
└── Abstracts                               # 抽象类
    ├── AbstractController.php              # 控制器抽象类
    ├── AbstractMapper.php                  # 数据映射抽象类
    ├── AbstractService.php                 # 服务层抽象类
└── Annotation                              # 注解类
    ├── Transaction.php                     # 数据库事务注解
└── Aspect                                  # 切面类
    ├── TransactionAspect.php               # 数据库事务切面类
└── Exception                               # 异常类
└── Traits                                  # 特征类
└── Collection.php                          # 集合类
└── FormRequest.php                         # 请求验证类
└── Model.php                               # 模型类
└── Request.php                             # 请求类
└── Response.php                            # 响应类
...

```

# 安装

```bash
composer require ydg/hyperf-admin-core
```